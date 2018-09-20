<?php
declare(strict_types=1);

namespace Http\Client\Curl\Request\Builder;

use Http\Client\Curl\Request\CurlRequest;
use Http\Message\RequestFactory;
use Http\Message\UriFactory;

class RequestBuilder
{
    private $uriFactory;

    private $requestFactory;

    private $method;

    private $user;

    private $password;

    private $scheme;

    private $host;

    private $port;

    private $uri;

    private $body;

    private $parameters;

    private $headers = [];

    private $options = [];

    private $authType = 0;

    private $ssl = true;

    private $noProcess = false;

    private $returnHeaders = false;

    private $timeoutMs = 0;

    private $connectMs = 0;

    private $protocol = '1.1';

    public function __construct(UriFactory $uriFactory, RequestFactory $requestFactory)
    {
        $this->uriFactory = $uriFactory;
        $this->requestFactory = $requestFactory;
    }

    public function auth(string $username, string $password, int $authType = 0): RequestBuilder
    {
        return $this->authType($authType)->user($username, $password);
    }

    public function user(string $username, string $password): RequestBuilder
    {
        $this->user = $username;
        $this->password = $password;

        return $this;
    }

    public function authType(int $authType): RequestBuilder
    {
        $this->authType = $authType;

        return $this;
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
        $this->timeoutMs = $msec;

        return $this;
    }

    public function connect(float $sec): RequestBuilder
    {
        return $this->connectMs((int)($sec * 1000));
    }

    public function connectMs(int $msec): RequestBuilder
    {
        $this->connectMs = $msec;

        return $this;
    }

    public function header(string $name, string $value): RequestBuilder
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function headers(array $headers): RequestBuilder
    {
        $this->headers = array_replace($this->headers, $headers);

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
        $this->options = array_replace($this->options, $options);

        return $this;
    }

    public function uri(string $uri, array $parameters = []): RequestBuilder
    {
        $this->uri = ltrim($uri, '/');
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

    public function noprocess() : RequestBuilder
    {
        $this->noProcess = true;

        return $this;
    }

    public function xml() : RequestBuilder
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
        return $this->header('Content-Encoding', $encoding);
    }

    public function host(string $host): RequestBuilder
    {
        $matches = [];
        $count = preg_match(
            '/^(?<scheme>(http|https))?(?<separator>\:\/\/)?(?<host>[a-zA-Z\d\.\-\/]+):?(?<port>\d+)?\/*$/',
            $host,
            $matches
        );
        if (0 === $count) {
            $this->host = rtrim($host, '/');

            return $this;
        }
        $this->host = $matches['host'];
        if (array_key_exists('scheme', $matches) && '' !== $matches['scheme']) {
            $this->scheme = (string)$matches['scheme'];
        }
        if (array_key_exists('port', $matches) && '' !== $matches['port']) {
            $this->port = (int)$matches['port'];
        }

        return $this;
    }

    public function port(int $port): RequestBuilder
    {
        $this->port = $port;

        return $this;
    }

    public function nossl(): RequestBuilder
    {
        $this->ssl = false;

        return $this;
    }

    public function responseHeaders(bool $return = false): RequestBuilder
    {
        $this->returnHeaders = $return;

        return $this;
    }

    public function referer(string $referer): RequestBuilder
    {
        return $this->header('Referer', $referer);
    }

    public function userAgent(string $userAgent): RequestBuilder
    {
        return $this->header('User-Agent', $userAgent);
    }

    public function clean()
    {
        $this->method = $this->user = $this->password = $this->scheme =
        $this->host = $this->port = $this->uri = $this->body = null;
        $this->parameters = $this->headers = $this->options = [];
        $this->ssl = true;
        $this->returnHeaders = $this->noProcess = false;
        $this->authType = $this->connectMs = $this->timeoutMs = 0;
        $this->protocol = '1.1';
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
        $url = $this->uri;
        $count = preg_match_all('/\{([a-zA-Z0-9\-\_]+)\}/', $url, $matches);
        if (false === $count) {
            throw new \RuntimeException('Cannot check placeholders in uri %s', $url);
        }
        if (0 !== $count) {
            $parameters = [];
            if ([] !== $this->parameters) {
                $parameters = $this->parameters;
            }
            if (is_array($this->body)) {
                $parameters = array_merge($this->parameters, $this->body);
            }
            if ([] === $parameters) {
                throw new \LogicException(
                    sprintf(
                        'Uri %s has placeholders but you didn\'t specify neither parameters nor appropriate body',
                        $url
                    )
                );
            }
            $search = [];
            $replacement = [];
            foreach (array_unique($matches[1]) as $placeHolder) {
                if (false === array_key_exists($placeHolder, $parameters)) {
                    throw new \LogicException(
                        sprintf(
                            'Uri %s has placeholder {%s} but neither parameters nor body have value for that',
                            $url,
                            $placeHolder
                        )
                    );
                }
                $search[] = sprintf('{%s}', $placeHolder);
                $replacement[] = $parameters[$placeHolder];
            }
            $url = str_replace($search, $replacement, $url);
        }
        $body = $this->body;
        switch (strtoupper($this->method)) {
            case 'POST':
            case 'PUT':
            case 'PATCH':
                if (false === array_key_exists('Content-Type', $this->headers)) {
                    break;
                }
                if ($this->noProcess) {
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
                    $url .= '?' . http_build_query($body);
                    $body = null;
                }
        }
        $uri = '';
        if ($this->scheme) {
            $uri .= $this->scheme . '://';
        }
        if ($this->user) {
            $uri .= $this->user . ($this->password ? ':' . $this->password : '') . '@';
        }
        $uri .= $this->host;
        if (null !== $this->port) {
            $uri .= ':' . $this->port;
        }
        $uri .= '/' . $url;
        $request = $this->requestFactory
            ->createRequest(
                $this->method,
                $uri,
                $this->headers,
                $body,
                $this->protocol
            );

        $request = new CurlRequest(
            $request,
            $this->connectMs,
            $this->timeoutMs,
            $this->ssl,
            $this->authType,
            $this->returnHeaders,
            $this->options
        );
        $this->clean();

        return $request;
    }
}
