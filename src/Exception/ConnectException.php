<?php
declare(strict_types=1);

namespace Http\Client\Curl\Exception;

use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;

class ConnectException extends AbstractNetworkException
{
}
