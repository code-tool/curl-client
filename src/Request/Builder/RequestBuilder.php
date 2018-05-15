<?php
declare(strict_types=1);

namespace Http\Client\Curl\Request\Builder;

use Http\Client\Curl\Request\CurlRequest;
use Http\Message\RequestFactory;

class RequestBuilder
{
    private $requestFactory;

    private $method;

    private $uri;

    private $body;

    private $parameters;

    private $headers = [];

    private $options = [];

    private $protocol = '1.1';

    public function __construct(RequestFactory $requestFactory)
    {
        $this->requestFactory = $requestFactory;
    }

    public function auth(string $username, string $password, string $type = ''): RequestBuilder
    {
        $this->curl(CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));
        if ('' !== $type) {
            $this->curl(CURLOPT_HTTPAUTH, $type);
        }

        return $this;
    }

    public function user(string $username, string $password): RequestBuilder
    {
        return $this->curl(CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));
    }

    public function authType(string $authType): RequestBuilder
    {
        return $this->curl(CURLOPT_HTTPAUTH, $authType);
    }

    public function method(string $method): RequestBuilder
    {
        $this->method = $method;

        return $this;
    }

    public function delete(): RequestBuilder
    {
        return $this->method('DELETE');
    }

    public function get(): RequestBuilder
    {
        return $this->method('GET');
    }

    public function head(): RequestBuilder
    {
        return $this->method('HEAD');
    }

    public function patch(): RequestBuilder
    {
        return $this->method('PATCH');
    }

    public function post(): RequestBuilder
    {
        return $this->method('POST');
    }

    public function put(): RequestBuilder
    {
        return $this->method('PUT');
    }

    public function options(): RequestBuilder
    {
        return $this->method('OPTIONS');
    }

    public function body($body): RequestBuilder
    {
        $this->body = $body;

        return $this;
    }

    public function timeout(float $sec): RequestBuilder
    {
        return $this->timeoutMs((int)($sec * 1000));
    }

    public function timeoutMs(int $msec): RequestBuilder
    {
        $this->options[CURLOPT_TIMEOUT_MS] = $msec;

        return $this;
    }

    public function connect(float $sec): RequestBuilder
    {
        return $this->connectMs((int)($sec * 1000));
    }

    public function connectMs(int $msec): RequestBuilder
    {
        $this->options[CURLOPT_CONNECTTIMEOUT_MS] = $msec;

        return $this;
    }

    public function header(string $name, string $value): RequestBuilder
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function headers(array $headers): RequestBuilder
    {
        $this->headers = $headers;

        return $this;
    }

    public function authorization(string $value): RequestBuilder
    {
        return $this->header('Authorization', $value);
    }

    public function contentType(string $value): RequestBuilder
    {
        return $this->header('Content-Type', $value);
    }

    public function curl(int $option, $value): RequestBuilder
    {
        $this->options[$option] = $value;

        return $this;
    }

    public function curls(array $options): RequestBuilder
    {
        $this->options = $options;

        return $this;
    }

    public function uri(string $uri, array $parameters = []): RequestBuilder
    {
        $this->uri = $uri;
        $this->parameters = $parameters;

        return $this;
    }

    public function protocol(string $protocol): RequestBuilder
    {
        $this->protocol = $protocol;

        return $this;
    }

    public function text(): RequestBuilder
    {
        return $this->contentType('text/html');
    }

    public function json(): RequestBuilder
    {
        return $this->contentType('application/json');
    }

    public function xml(): RequestBuilder
    {
        return $this->contentType('application/xml');
    }

    public function encoded(): RequestBuilder
    {
        return $this->contentType('application/x-www-form-urlencoded');
    }

    public function formData(): RequestBuilder
    {
        return $this->contentType('multipart/form-data');
    }

    public function encoding(string $encoding): RequestBuilder
    {
        return $this->curl(CURLOPT_ENCODING, $encoding);
    }

    public function port(int $port): RequestBuilder
    {
        return $this->curl(CURLOPT_PORT, $port);
    }

    public function nossl(): RequestBuilder
    {
        return $this
            ->curl(CURLOPT_SSL_VERIFYPEER, false)
            ->curl(CURLOPT_SSL_VERIFYHOST, false);
    }

    public function responseHeaders(bool $return = false): RequestBuilder
    {
        return $this->curl(CURLOPT_HEADER, $return);
    }

    public function referer(string $referer): RequestBuilder
    {
        return $this->curl(CURLOPT_REFERER, $referer);
    }

    public function userAgent(string $userAgent): RequestBuilder
    {
        return $this->curl(CURLOPT_USERAGENT, $userAgent);
    }

    public function build(): CurlRequest
    {
        if (null === $this->method) {
            throw new \RuntimeException('Request must be defined with method');
        }

        if (null === $this->uri) {
            throw new \RuntimeException('Request must be defined with uri');
        }


        $matches = [];
        $uri = $this->uri;
        preg_match('/\{([a-zA-Z0-9\-\_]+)\}/', $uri, $matches);
        if (count($matches) > 1) {
            $parameters = [];
            if ([] !== $this->parameters) {
                $parameters = $this->parameters;
            }
            if (is_array($this->body)) {
                $parameters = array_merge($this->parameters, $this->body);
            }
            if ([] === $parameters) {
                throw new \LogicException(
                    sprintf('Uri %s has placeholders but you didn\'t specify neither parameters nor body', $uri)
                );
            }
            foreach ($matches[1] as $placeHolder) {
                if (false === array_key_exists($placeHolder, $parameters)) {
                    throw new \LogicException(
                        sprintf(
                            'Uri %s has placeholder but neither parameters nor body have value for %s',
                            $uri,
                            $placeHolder
                        )
                    );
                }
                $search[] = sprintf('{%s}', $placeHolder);
                $replacement[] = $parameters[$placeHolder];
            }
            $uri = str_replace($search, $replacement, $uri);
        }

        $body = $this->body;
        switch (strtoupper($this->method)) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                if (false === array_key_exists('Content-Type', $this->headers)) {
                    break;
                }
                switch ($this->headers['Content-Type']) {
                    case 'application/json':
                        $body = json_encode($body);
                        break;
                    case 'application/x-www-form-urlencoded':
                        $body = http_build_query($body);
                        break;
                    default:
                        break;
                }
                break;
            default:
                if (null !== $body) {
                    $uri .= '?' . http_build_query($body);
                    $body = null;
                }
        }

        $request = $this->requestFactory
            ->createRequest(
                $this->method,
                $uri,
                $this->headers,
                $body,
                $this->protocol
            );

        $this->method = $this->uri = $this->body = null;
        $this->parameters = $this->headers = $this->options = [];
        $this->protocol = '1.1';

        return new CurlRequest($request, $this->options);
    }
}
