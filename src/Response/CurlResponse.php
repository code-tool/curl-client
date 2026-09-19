<?php
declare(strict_types=1);

namespace Http\Client\Curl\Response;

use Http\Client\Curl\CurlInfo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CurlResponse implements ResponseInterface
{
    private $response;

    private $curlInfo;

    public function __construct(ResponseInterface $response, CurlInfo $curlInfo)
    {
        $this->response = $response;
        $this->curlInfo = $curlInfo;
    }

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * @return static
     */
    public function withProtocolVersion($version): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withProtocolVersion($version);

        return $copy;
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader($name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name): string
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * @return static
     */
    public function withHeader($name, $value): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withHeader($name, $value);

        return $copy;
    }

    /**
     * @return static
     */
    public function withAddedHeader($name, $value): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withAddedHeader($name, $value);

        return $copy;
    }

    /**
     * @return static
     */
    public function withoutHeader($name): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withoutHeader($name);

        return $copy;
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * @return static
     */
    public function withBody(StreamInterface $body): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withBody($body);

        return $copy;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return static
     */
    public function withStatus($code, $reasonPhrase = ''): self
    {
        $copy = clone $this;
        $copy->response = $this->response->withStatus($code, $reasonPhrase);

        return $copy;
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }

    public function curlInfo(): CurlInfo
    {
        return $this->curlInfo;
    }
}
