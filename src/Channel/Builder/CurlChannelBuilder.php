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

    private $result;

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
     */    public function setMethod()
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

    /**
     * @return CurlChannelBuilder
     */
    public function setBodySize()
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

    /**
     * @return CurlChannelBuilder
     */
    public function setCallbacks()
    {
        $this->options[CURLOPT_HEADERFUNCTION] = [$this->result, 'headers'];
        $this->options[CURLOPT_WRITEFUNCTION] = [$this->result, 'write'];
        $this->options[CURLOPT_READFUNCTION] = [$this->result, 'read'];

        return $this;
    }

    /**
     * @return CurlChannel
     */
    public function getChannel()
    {
        $this
            ->consistent();

        $this->result = new CurlChannel($this->channel, $this->request, $this->response);

        $this->reset()
            ->setMethod()
            ->setBodySize()
            ->setHttpVersion()
            ->setUrl()
            ->setHeaders()
            ->setUserInfo()
            ->setCallbacks()
            ->setOptions();

        return $this->result;
    }
}
