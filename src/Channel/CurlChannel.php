<?php
declare(strict_types=1);

namespace Http\Client\Curl\Channel;

use Http\Client\Curl\CurlInfo;
use Http\Client\Curl\Exception\ConnectException;
use Http\Client\Curl\Exception\RequestException;
use Http\Client\Curl\Exception\ResolveException;
use Http\Client\Curl\Response\CurlResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlChannel
{
    private $channel;

    private $request;

    private $response;

    private $current;

    /**
     * CurlChannel constructor.
     *
     * @param                   $channel
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function __construct($channel, RequestInterface $request, ResponseInterface $response)
    {
        $this->channel = $channel;
        $this->request = $request;
        $this->response = $response;
        $this->current = $response;
    }

    public function read($channel, $fileDescriptor, $length): string
    {
        return $this->request->getBody()->read($length);
    }

    public function write($channel, $data): int
    {
        return $this->response->getBody()->write($data);
    }

    public function headers($channel, $data): int
    {
        $str = trim($data);
        if ('' === $str) {
            return strlen($data);
        }
        if (0 === stripos($str, 'http/')) {
            $this->current = clone $this->response;
            [$protocol, $code,] = explode(' ', $str, 3);
            $this->current = $this->current->withStatus((int)$code);

            return strlen($data);
        }
        [$name, $value,] = explode(':', $str, 2);
        $name = trim($name);
        $value = trim($value);
        if ($this->current->hasHeader($name)) {
            $this->current = $this->current->withAddedHeader($name, $value);
        } else {
            $this->current = $this->current->withHeader($name, $value);
        }

        return strlen($data);
    }

    public function send(): CurlResponse
    {
        curl_exec($this->channel);
        switch (curl_errno($this->channel)) {
            case CURLE_OK:
                return new CurlResponse($this->current, new CurlInfo(curl_getinfo($this->channel)));
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
                throw new ResolveException($this->request, curl_error($this->channel));
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new ConnectException($this->request, curl_error($this->channel));
            default:
                throw new RequestException($this->request, curl_error($this->channel));
        }
    }
}
