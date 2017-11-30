<?php
declare(strict_types=1);

namespace Http\Client\Curl\Decorator;

use Http\Client\Curl\CurlClient;
use Http\Client\Curl\CurlClientInterface;
use Http\Client\Curl\Response\CurlResponse;
use Psr\Http\Message\RequestInterface;

abstract class AbstractCurlClientDecorator implements CurlClientInterface
{
    private $curlClient;

    public function __construct(CurlClient $curlClient)
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

    public function setOption(int $option, $value): CurlClientInterface
    {
        $this->curlClient->setOption($option, $value);

        return $this;
    }

    public function setRequestOption(int $option, $value): CurlClientInterface
    {
        $this->curlClient->setRequestOption($option, $value);

        return $this;
    }
}
