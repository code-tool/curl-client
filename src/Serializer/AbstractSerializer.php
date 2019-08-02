<?php
declare(strict_types=1);

namespace Http\Client\Curl\Serializer;

use Psr\Http\Message\MessageInterface;

abstract class AbstractSerializer implements SerializerInterface
{
    abstract public function getThreshold(): ?int;

    public function getHeaders(MessageInterface $message): array
    {
        $headers = [];
        foreach ($message->getHeaders() as $header => $values) {
            $headers[$header] = implode('; ', $values);
        }

        return $headers;
    }

    public function getBody(MessageInterface $message): string
    {
        try {
            if ($this->getThreshold() > $message->getBody()->getSize()) {
                return \sprintf('Body is longer than %d bytes', (int)$this->getThreshold());
            }

            return (string)$message->getBody();
        } finally {
            if ($message->getBody()->isSeekable()) {
                $message->getBody()->rewind();
            }
        }
    }
}
