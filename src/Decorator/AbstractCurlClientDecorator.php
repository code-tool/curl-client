<?php

namespace Http\Client\Curl\Decorator;

use Http\Client\Curl\CurlClientInterface;
use Http\Client\Curl\Request\CurlRequest;
use Http\Client\Curl\Response\CurlResponse;

abstract class AbstractCurlClientDecorator implements CurlClientInterface
{
    private $curlClient;

    public function __construct(CurlClientInterface $curlClient)
    {
        $this->curlClient = $curlClient;
    }

    /**
     * @param CurlRequest $request
     *
     * @return CurlResponse
     */
    public function send(CurlRequest $request)
    {
        return $this->curlClient->send($request);
    }
}
