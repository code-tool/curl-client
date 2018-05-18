<?php
declare(strict_types=1);

namespace Http\Client\Curl\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class CurlRequest implements RequestInterface
{
    private $request;

    private $options;

    public function __construct(RequestInterface $request, array $options)
    {
        $this->request = $request;
        $this->options = $options;
    }

    public function options(): array
    {
        return $this->options;
    }

    public function getRequestTarget()
    {
        return $this->request->getRequestTarget();
    }

    public function withRequestTarget($requestTarget)
    {
        $copy = clone $this;
        $copy->request = $this->request->withRequestTarget($requestTarget);

        return $copy;
    }

    public function getMethod()
    {
        return $this->request->getMethod();
    }

    public function withMethod($method)
    {
        $copy = clone $this;
        $copy->request = $this->request->withMethod($method);

        return $copy;
    }

    public function getUri()
    {
        return $this->request->getUri();
    }

    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $copy = clone $this;
        $copy->request = $this->request->withUri($uri, $preserveHost);

        return $copy;
    }

    public function getProtocolVersion()
    {
        return $this->request->getProtocolVersion();
    }

    public function withProtocolVersion($version)
    {
        $copy = clone $this;
        $copy->request = $this->request->withProtocolVersion($version);

        return $copy;
    }

    public function getHeaders()
    {
        return $this->request->getHeaders();
    }

    public function hasHeader($name)
    {
        return $this->request->hasHeader($name);
    }

    public function getHeader($name)
    {
        return $this->request->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->request->getHeaderLine($name);
    }

    public function withHeader($name, $value)
    {
        $copy = clone $this;
        $copy->request = $this->request->withHeader($name, $value);

        return $copy;
    }

    public function withAddedHeader($name, $value)
    {
        $copy = clone $this;
        $copy->request = $this->request->withAddedHeader($name, $value);

        return $copy;
    }

    public function withoutHeader($name)
    {
        $copy = clone $this;
        $copy->request = $this->request->withoutHeader($name);

        return $copy;
    }

    public function getBody()
    {
        return $this->request->getBody();
    }

    public function withBody(StreamInterface $body)
    {
        $copy = clone $this;
        $copy->request = $this->request->withBody($body);

        return $copy;
    }

    public function toArray()
    {
        $headers = [];
        foreach ($this->getHeaders() as $header => $values) {
            $headers[$header] = implode('; ', $values);
        }

        return [
            'host' => $this->getUri()->getHost(),
            'method' => $this->getMethod(),
            'uri' => $this->getUri()->getPath(),
            'headers' => $headers,
            'body' => $this->getBody()->__toString(),
        ];
    }
}
