<?php
declare(strict_types=1);

namespace Http\Client\Curl\Channel\Builder;

use Http\Client\Curl\Options\CurlChannel;
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

    public function option(int $option, $value)
    {
        $this->options[$option] = $value;

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

    public function setOptions()
    {
        $this->options[CURLOPT_HEADER] = false;
        $this->options[CURLOPT_RETURNTRANSFER] = false;
        $this->options[CURLOPT_FOLLOWLOCATION] = false;

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
        $this->options[CURLOPT_URL] = (string)$this->request->getUri()->__toString();

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


    public function options(array $options): CurlChannelBuilder
    {
        $this->options = $options;

        return $this;
    }


    public function setCallbacks(): CurlChannelBuilder
    {
        $options[CURLOPT_HEADERFUNCTION] = function ($channel, $data) {
            $str = trim($data);
            if ('' !== $str) {
                if (strpos(strtolower($str), 'http/') === 0) {
                    $this->response = $this->response->withStatus((int)$str);
                } else {
                    list ($name, $value,) = explode(':', $str, 2);
                    $name = trim($name);
                    $value = trim($value);
                    if ($this->response->hasHeader($name)) {
                        $this->response = $this->response->withAddedHeader($name, $value);
                    } else {
                        $this->response = $this->response->withHeader($name, $value);
                    }
                }
            }

            return strlen($data);
        };

        $options[CURLOPT_WRITEFUNCTION] = function ($channel, $data) {
            return $this->response->getBody()->write($data);
        };

        $options[CURLOPT_READFUNCTION] = function ($channel, $fileDescriptor, $length) {
            return $this->request->getBody()->read($length);
        };

        return $this;
    }

    public function getChannel(): CurlChannel
    {
        $this
            ->consistent()
            ->reset()
            ->setOptions()
            ->setHttpVersion()
            ->setUrl()
            ->setHeaders()
            ->setUserInfo()
            ->setCallbacks();

        $channel = new CurlChannel($this->channel, $this->request, $this->response);
        $this->request = null;
        $this->response = null;
        $this->channel = null;

        return $channel;
    }
}
