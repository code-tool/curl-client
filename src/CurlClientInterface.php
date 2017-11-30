<?php
declare(strict_types=1);

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
    public function setOption(int $option, $value): CurlClientInterface;

    public function setRequestOption(int $option, $value): CurlClientInterface;
}
