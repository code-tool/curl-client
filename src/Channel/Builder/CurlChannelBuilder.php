<?php

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

    /**
     * @param $channel
     *
     * @return CurlChannelBuilder
     */
    public function channel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @param RequestInterface $request
     *
     * @return CurlChannelBuilder
     */
    public function request(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return CurlChannelBuilder
     */
    public function response(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlChannelBuilder
     */
    public function option($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function consistent()
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

    /**
     * @return CurlChannelBuilder
     */
    public function reset()
    {
        curl_reset($this->channel);

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setOptions()
    {
        $this->options[CURLOPT_HEADER] = false;
        $this->options[CURLOPT_RETURNTRANSFER] = false;
        $this->options[CURLOPT_FOLLOWLOCATION] = false;

        curl_setopt_array($this->channel, $this->options);

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setHttpVersion()
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

    /**
     * @return CurlChannelBuilder
     */
    public function setUrl()
    {
        $this->options[CURLOPT_URL] = (string)$this->request->getUri()->__toString();

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setHeaders()
    {
        foreach ($this->request->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                $this->options[CURLOPT_HTTPHEADER][] = sprintf('%s: %s', $name, $value);
            }
        }

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setUserInfo()
    {
        if ('' === ($userInfo = $this->request->getUri()->getUserInfo())) {
            return $this;
        }
        $this->options[CURLOPT_USERPWD] = $userInfo;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return CurlChannelBuilder
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return CurlChannelBuilder
     */
    public function setCallbacks()
    {
        $this->options[CURLOPT_HEADERFUNCTION] = function ($channel, $data) {
            $str = trim($data);
            if ('' !== $str) {
                if (strpos(strtolower($str), 'http/') === 0) {
                    list ($protocol, $code,) = explode(' ', $str, 3);
                    $this->response = $this->response->withStatus((int)$code);
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

        $this->options[CURLOPT_WRITEFUNCTION] = function ($channel, $data) {
            return $this->response->getBody()->write($data);
        };

        $this->options[CURLOPT_READFUNCTION] = function ($channel, $fileDescriptor, $length) {
            return $this->request->getBody()->read($length);
        };

        return $this;
    }

    /**
     * @return CurlChannel
     */
    public function getChannel()
    {
        $this
            ->consistent()
            ->reset()
            ->setHttpVersion()
            ->setUrl()
            ->setHeaders()
            ->setUserInfo()
            ->setCallbacks()
            ->setOptions();

        $channel = new CurlChannel($this->channel, $this->request, $this->response);

        return $channel;
    }
}
