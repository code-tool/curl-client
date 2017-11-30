<?php
declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Client\Curl\Channel\Builder\CurlChannelBuilder;
use Http\Client\Curl\Response\CurlResponse;
use Http\Message\ResponseFactory as ResponseFactoryInterface;
use Psr\Http\Message\RequestInterface;

class CurlClient implements CurlClientInterface
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

    public function setOption(int $option, $value): CurlClientInterface
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function setRequestOption(int $option, $value): CurlClientInterface
    {
        $this->requestOptions[$option] = $value;

        return $this;
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

        $this->requestOptions = [];

        return $response;
    }
}
