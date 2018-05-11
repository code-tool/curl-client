<?php
declare(strict_types=1);

namespace Http\Client\Curl;

use Http\Client\Curl\Request\CurlRequest;
use Http\Client\Curl\Response\CurlResponse;

/**
 * Interface CurlClientInterface
 */
interface CurlClientInterface
{
    public function send(CurlRequest $request) : CurlResponse;
}
