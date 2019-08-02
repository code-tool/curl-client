<?php
declare(strict_types=1);

namespace Http\Client\Curl\Serializer;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface SerializerInterface
{
    public function request(RequestInterface $request): array;

    public function response(ResponseInterface $response): array;
}
