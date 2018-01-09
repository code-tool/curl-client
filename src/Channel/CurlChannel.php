<?php
namespace Http\Client\Curl\Channel;

use Http\Client\Curl\Response\CurlResponse;
use Http\Client\Exception\NetworkException;
use Http\Client\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlChannel
{
    private $channel;

    private $request;

    private $response;

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
    }

    /**
     * @param resource $channel
     * @param resource $fileDescriptor
     * @param int $length
     *
     * @return string
     */
    public function read($channel, $fileDescriptor, $length)
    {
        return $this->request->getBody()->read($length);
    }

    /**
     * @param resource $channel
     * @param string $data
     *
     * @return int
     */
    public function write($channel, $data)
    {
        return $this->response->getBody()->write($data);
    }

    /**
     * @param resource $channel
     * @param string $data
     *
     * @return int
     */
    public function headers($channel, $data)
    {
        $str = trim($data);
        if ('' === $str) {
            return strlen($data);
        }

        if (strpos(strtolower($str), 'http/') === 0) {
            list ($protocol, $code,) = explode(' ', $str, 3);
            $this->response = $this->response->withStatus((int)$code);

            return strlen($data);
        }

        list ($name, $value,) = explode(':', $str, 2);
        $name = trim($name);
        $value = trim($value);
        if ($this->response->hasHeader($name)) {
            $this->response = $this->response->withAddedHeader($name, $value);
        } else {
            $this->response = $this->response->withHeader($name, $value);
        }

        return strlen($data);
    }

    /**
     * @return CurlResponse
     */
    public function send()
    {
        curl_exec($this->channel);
        switch (curl_errno($this->channel)) {
            case CURLE_OK:
                return new CurlResponse($this->response, curl_getinfo($this->channel));
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new NetworkException(curl_error($this->channel), $this->request);
            default:
                throw new RequestException(curl_error($this->channel), $this->request);
        }
    }
}
