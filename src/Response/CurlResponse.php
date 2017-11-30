<?php
namespace Http\Client\Curl\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CurlResponse implements ResponseInterface
{
    private $response;

    private $curlInfo;

    public function __construct(ResponseInterface $response, array $curlInfo)
    {
        $this->response = $response;
        $this->curlInfo = $curlInfo;
    }

    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version)
    {
        $copy = clone $this;
        $copy->response = $this->response->withProtocolVersion($version);

        return $copy;
    }

    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader($name, $value)
    {
        $copy = clone $this;
        $copy->response = $this->response->withHeader($name, $value);

        return $copy;
    }

    public function withAddedHeader($name, $value)
    {
        $copy = clone $this;
        $copy->response = $this->response->withAddedHeader($name, $value);

        return $copy;
    }

    public function withoutHeader($name)
    {
        $copy = clone $this;
        $copy->response = $this->response->withoutHeader($name);

        return $copy;
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body)
    {
        $copy = clone $this;
        $copy->response = $this->response->withBody($body);

        return $copy;
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $copy = clone $this;
        $copy->response = $this->response->withStatus($code, $reasonPhrase);

        return $copy;
    }

    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }

    /**
     * @return array
     */
    public function getCurlInfo()
    {
        return $this->curlInfo;
    }
}
