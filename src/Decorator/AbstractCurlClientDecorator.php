<?php

namespace Http\Client\Curl\Decorator;

use Http\Client\Curl\CurlClientInterface;
use Http\Client\Curl\Response\CurlResponse;
use Psr\Http\Message\RequestInterface;

abstract class AbstractCurlClientDecorator implements CurlClientInterface
{
    private $curlClient;

    public function __construct(CurlClientInterface $curlClient)
    {
        $this->curlClient = $curlClient;
    }

    /**
     * @param RequestInterface $request
     *
     * @return CurlResponse
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->curlClient->sendRequest($request);
    }

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setOption($option, $value)
    {
        $this->curlClient->setOption($option, $value);

        return $this;
    }

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setRequestOption($option, $value)
    {
        $this->curlClient->setRequestOption($option, $value);

        return $this;
    }
}
