<?php
namespace Http\Client\Curl;

use Http\Client\Curl\Request\CurlRequest;
use Http\Client\Curl\Response\CurlResponse;

/**
 * Interface CurlClientInterface
 */
interface CurlClientInterface
{
    /**
     * @param CurlRequest $request
     *
     * @return CurlResponse
     */
    public function send(CurlRequest $request);
}
