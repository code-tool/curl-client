<?php
declare(strict_types=1);

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

    public function send(CurlRequest $request): CurlResponse
    {
        return $this->curlClient->send($request);
    }
}
