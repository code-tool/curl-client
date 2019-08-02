<?php
declare(strict_types=1);

namespace Http\Client\Curl\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShortCurlSerializer extends AbstractSerializer
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
            'uri' => $request->getUri()->getPath(),
            'body' => $this->getBody($request),
        ];
    }

    public function response(ResponseInterface $response): array
    {
        return ['status' => $response->getStatusCode(), 'body' => $this->getBody($response),];
    }
}
