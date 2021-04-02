<?php
declare(strict_types=1);

namespace Http\Client\Curl;

use CurlHandle;
use Http\Client\Curl\Channel\Builder\CurlChannelBuilder;
use Http\Client\Curl\Request\CurlRequest;
use Http\Client\Curl\Response\CurlResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class CurlClient implements CurlClientInterface, ClientInterface
{
    /**
     * @var resource|CurlHandle|false
     */
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
        if (false === $this->resource || null === $this->resource) {
            return;
        }
        curl_close($this->resource);
    }

    public function send(CurlRequest $request): CurlResponse
    {
        $this->requestOptions = $request->options();

        return $this->sendRequest($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return CurlResponse
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        if (false === $this->resource || null === $this->resource) {
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
