<?php
declare(strict_types=1);

namespace Http\Client\Curl\Channel\Builder;

use Http\Client\Curl\Channel\CurlChannel;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlChannelBuilder
{
    private $options;

    private $channel;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     */
    private $response;

    private $result;

    public function channel($channel): CurlChannelBuilder
    {
        $this->channel = $channel;

        return $this;
    }

    public function request(RequestInterface $request): CurlChannelBuilder
    {
        $this->request = $request;

        return $this;
    }

    public function response(ResponseInterface $response): CurlChannelBuilder
    {
        $this->response = $response;

        return $this;
    }

    public function option(int $option, $value): CurlChannelBuilder
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function options(array $options): CurlChannelBuilder
    {
        $this->options = $options;

        return $this;
    }

    public function consistent(): CurlChannelBuilder
    {
        if (false === is_resource($this->channel)) {
            throw new \RuntimeException('You forgot to set channel resource');
        }
        if (null === $this->request) {
            throw new \RuntimeException('You forgot to set request object');
        }
        if (null === $this->response) {
            throw new \RuntimeException('You forgot to set response object');
        }

        return $this;
    }

    public function reset(): CurlChannelBuilder
    {
        curl_reset($this->channel);

        return $this;
    }

    public function rewind(): CurlChannelBuilder
    {
        $this->request->getBody()->rewind();
        $this->response->getBody()->rewind();

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setOptions()
    {
        if (false === array_key_exists(CURLOPT_HEADER, $this->options)) {
            $this->options[CURLOPT_HEADER] = false;
        }
        $this->options[CURLOPT_FOLLOWLOCATION] = false;
        curl_setopt_array($this->channel, $this->options);

        return $this;
    }

    public function setHttpVersion(): CurlChannelBuilder
    {
        switch ($this->request->getProtocolVersion()) {
            case '1.0':
                $this->options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_0;
                break;
            case '1.1':
                $this->options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_1_1;
                break;
            case '2.0':
                $this->options[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
                break;
        }

        return $this;
    }

    public function setUrl(): CurlChannelBuilder
    {
        $this->options[CURLOPT_URL] = $this->request->getUri()->__toString();

        return $this;
    }

    public function setHeaders(): CurlChannelBuilder
    {
        foreach ($this->request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $this->options[CURLOPT_HTTPHEADER][] = sprintf('%s: %s', $name, $value);
            }
        }

        return $this;
    }

    public function setUserInfo(): CurlChannelBuilder
    {
        if ('' === ($userInfo = $this->request->getUri()->getUserInfo())) {
            return $this;
        }
        $this->options[CURLOPT_USERPWD] = $userInfo;

        return $this;
    }

    public function setPort(): CurlChannelBuilder
    {
        $this->options[CURLOPT_PORT] = $this->request->getUri()->getPort();

        return $this;
    }

    public function setMethod(): CurlChannelBuilder
    {
        switch (strtoupper($this->request->getMethod())) {
            case 'GET':
                break;
            case 'HEAD':
                $this->options[CURLOPT_NOBODY] = true;
                break;
            default:
                $this->options[CURLOPT_CUSTOMREQUEST] = $this->request->getMethod();
                break;
        }

        return $this;
    }


    public function setBodySize(): CurlChannelBuilder
    {
        if (in_array(strtoupper($this->request->getMethod()), ['GET', 'HEAD', 'TRACE'])) {
            return $this;
        }

        $this->options[CURLOPT_UPLOAD] = true;
        if (null === ($size = $this->request->getBody()->getSize())) {
            return $this;
        }
        $this->options[CURLOPT_INFILESIZE] = $size;

        return $this;
    }

    public function setCallbacks(): CurlChannelBuilder
    {
        $this->options[CURLOPT_HEADERFUNCTION] = [$this->result, 'headers'];
        $this->options[CURLOPT_WRITEFUNCTION] = [$this->result, 'write'];
        $this->options[CURLOPT_READFUNCTION] = [$this->result, 'read'];

        return $this;
    }

    public function getChannel(): CurlChannel
    {
        $this
            ->consistent();

        $this->result = new CurlChannel($this->channel, $this->request, $this->response);

        $this
            ->reset()
            ->rewind()
            ->setMethod()
            ->setBodySize()
            ->setHttpVersion()
            ->setPort()
            ->setUrl()
            ->setHeaders()
            ->setUserInfo()
            ->setCallbacks()
            ->setOptions();

        return $this->result;
    }
}
