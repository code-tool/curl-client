<?php
declare(strict_types=1);

namespace Http\Client\Curl\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class CurlRequest implements RequestInterface, \JsonSerializable
{
    private $request;

    private $connectMs;

    private $timeoutMs;

    private $ssl;

    private $authType;

    private $returnHeaders;

    private $options;

    public function __construct(
        RequestInterface $request,
        int $connectMs,
        int $timeoutMs,
        bool $ssl,
        int $authType,
        bool $returnHeaders,
        array $options
    ) {
        $this->request = $request;
        $this->connectMs = $connectMs;
        $this->timeoutMs = $timeoutMs;
        $this->ssl = $ssl;
        $this->authType = $authType;
        $this->returnHeaders = $returnHeaders;
        $this->options = $options;
    }

    public function ssl(): bool
    {
        return $this->ssl;
    }

    public function options(): array
    {
        $options = $this->options;
        if (false === $this->ssl) {
            $options[CURLOPT_SSL_VERIFYPEER] = false;
            $options[CURLOPT_SSL_VERIFYHOST] = false;
        }
        if (0 !== $this->authType) {
            $options[CURLOPT_HTTPAUTH] = $this->authType;
        }
        if (0 !== $this->connectMs) {
            $options[CURLOPT_CONNECTTIMEOUT_MS] = $this->connectMs;
        }
        if (0 !== $this->timeoutMs) {
            $options[CURLOPT_TIMEOUT_MS] = $this->timeoutMs;
        }
        if ($this->returnHeaders) {
            $options[CURLOPT_HEADER] = true;
        }

        return $options;
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
        if (method_exists($this->request, 'getParsedBody')) {
            $body = $this->request->getParsedBody();
        } else {
            $body = $this->request->getBody()->getContents();
        }
        if ($this->request->getBody()->isSeekable()) {
            $this->request->getBody()->rewind();
        }

        return [
            'host' => $this->getUri()->getHost(),
            'method' => $this->getMethod(),
            'uri' => $this->getUri()->getPath(),
            'headers' => $headers,
            'body' => $body,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
