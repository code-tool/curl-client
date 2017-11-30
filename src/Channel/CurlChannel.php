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
