<?php
declare(strict_types=1);

namespace Http\Client\Curl\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

class ConnectException extends \RuntimeException implements NetworkExceptionInterface
{
    private $request;

    public function __construct(RequestInterface $request, string $message = '')
    {
        $this->request = $request;
        parent::__construct($message);
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
