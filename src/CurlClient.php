<?php

namespace Http\Client\Curl;

use Http\Client\Curl\Channel\Builder\CurlChannelBuilder;
use Http\Client\Curl\Request\CurlRequest;
use Http\Client\Curl\Response\CurlResponse;
use Http\Client\HttpClient;
use Http\Message\ResponseFactory as ResponseFactoryInterface;
use Psr\Http\Message\RequestInterface;

class CurlClient implements CurlClientInterface, HttpClient
{
    private $resource;

    private $options;

    private $requestOptions = [];

    private $channelBuilder;

    private $responseFactory;

    public function __construct(
        CurlChannelBuilder $channelBuilder,
        ResponseFactoryInterface $responseFactory,
        array $options = []
    ) {
        $this->channelBuilder = $channelBuilder;
        $this->responseFactory = $responseFactory;
        $this->options = $options;
    }

    public function __destruct()
    {
        if (false === is_resource($this->resource)) {
            return;
        }
        curl_close($this->resource);
    }

    /**
     * @param CurlRequest $request
     *
     * @return CurlResponse
     */
    public function send(CurlRequest $request)
    {
        $this->requestOptions = $request->options();

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return CurlResponse
     */
    public function sendRequest(RequestInterface $request)
    {
        if (false === is_resource($this->resource)) {
            $this->resource = curl_init();
        }

        $response = $this->channelBuilder
            ->channel($this->resource)
            ->options(array_replace($this->options, $this->requestOptions))
            ->request($request)
            ->response($this->responseFactory->createResponse())
            ->getChannel()
            ->send();

        $response->getBody()->rewind();
        $this->requestOptions = [];

        return $response;
    }
}
