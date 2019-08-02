<?php
declare(strict_types=1);

namespace Http\Client\Curl\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class FullCurlSerializer extends AbstractSerializer
{
    private $threshold;

    public function __construct(?int $threshold = 4096)
    {
        $this->threshold = $threshold;
    }

    public function getThreshold(): ?int
    {
        return $this->threshold;
    }

    public function request(RequestInterface $request): array
    {
        return [
            'host' => $request->getUri()->getHost(),
            'method' => $request->getMethod(),
            'uri' => $request->getUri()->getPath(),
            'headers' => $this->getHeaders($request),
            'body' => $this->getBody($request),
        ];
    }

    public function response(ResponseInterface $response): array
    {
        return [
            'headers' => $this->getHeaders($response),
            'body' => $this->getBody($response),
        ];
    }
}
