<?php
namespace Http\Client\Curl;

use Http\Client\Curl\Response\CurlResponse;
use Http\Client\HttpClient as HttpClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Interface CurlClientInterface
 *
 * @method CurlResponse sendRequest(RequestInterface $request)
 */
interface CurlClientInterface extends HttpClientInterface
{
    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setOption($option, $value);

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setRequestOption($option, $value);
}
