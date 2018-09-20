<?php
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

    private $path;

    private $query;

    private $fragment;

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

    /**
     * @param string $username
     * @param string $password
     * @param int    $authType
     *
     * @return RequestBuilder
     */
    public function auth($username, $password, $authType = 0)
    {
        return $this->authType($authType)->user($username, $password);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return RequestBuilder
     */
    public function user($username, $password)
    {
        $this->user = $username;
        $this->password = $password;

        return $this;
    }

    /**
     * @param int $authType
     *
     * @return RequestBuilder
     */
    public function authType($authType)
    {
        $this->authType = $authType;

        return $this;
    }

    /**
     * @param string $method
     *
     * @return RequestBuilder
     */
    public function method($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return RequestBuilder
     */
    public function delete()
    {
        return $this->method('DELETE');
    }

    /**
     * @return RequestBuilder
     */
    public function get()
    {
        return $this->method('GET');
    }

    /**
     * @return RequestBuilder
     */
    public function head()
    {
        return $this->method('HEAD');
    }

    /**
     * @return RequestBuilder
     */
    public function patch()
    {
        return $this->method('PATCH');
    }

    /**
     * @return RequestBuilder
     */
    public function post()
    {
        return $this->method('POST');
    }

    /**
     * @return RequestBuilder
     */
    public function put()
    {
        return $this->method('PUT');
    }

    /**
     * @return RequestBuilder
     */
    public function options()
    {
        return $this->method('OPTIONS');
    }

    /**
     * @param $body
     *
     * @return RequestBuilder
     */
    public function body($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param float $sec
     *
     * @return RequestBuilder
     */
    public function timeout($sec)
    {
        return $this->timeoutMs((int)($sec * 1000));
    }

    /**
     * @param int $msec
     *
     * @return RequestBuilder
     */
    public function timeoutMs($msec)
    {
        $this->timeoutMs = $msec;

        return $this;
    }

    /**
     * @param float $sec
     *
     * @return RequestBuilder
     */
    public function connect($sec)
    {
        return $this->connectMs((int)($sec * 1000));
    }

    /**
     * @param int $msec
     *
     * @return RequestBuilder
     */
    public function connectMs($msec)
    {
        $this->connectMs = $msec;

        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return RequestBuilder
     */
    public function header($name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param array $headers
     *
     * @return RequestBuilder
     */
    public function headers(array $headers)
    {
        $this->headers = array_replace($this->headers, $headers);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return RequestBuilder
     */
    public function authorization($value)
    {
        return $this->header('Authorization', $value);
    }

    /**
     * @param string $value
     *
     * @return RequestBuilder
     */
    public function contentType($value)
    {
        return $this->header('Content-Type', $value);
    }

    /**
     * @param int $option
     * @param     $value
     *
     * @return RequestBuilder
     */
    public function curl($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * @param array $options
     *
     * @return RequestBuilder
     */
    public function curls(array $options)
    {
        $this->options = array_replace($this->options, $options);

        return $this;
    }

    /**
     * @param string $uri
     * @param array  $parameters
     *
     * @return RequestBuilder
     */
    public function uri($uri, array $parameters = [])
    {
        if (false === ($parsed = parse_url(ltrim($uri, '/')))) {
            throw new \RuntimeException('Malformed url');
        }
        if (array_key_exists('scheme', $parsed) && '' !== $parsed['scheme']) {
            $this->scheme = (string)$parsed['scheme'];
        }
        if (array_key_exists('user', $parsed) && '' !== $parsed['user']) {
            $this->user = (string)$parsed['user'];
        }
        if (array_key_exists('pass', $parsed) && '' !== $parsed['pass']) {
            $this->password = (string)$parsed['pass'];
        }
        if (array_key_exists('host', $parsed) && '' !== $parsed['host']) {
            $this->host = (string)$parsed['host'];
        }
        if (array_key_exists('port', $parsed) && '' !== $parsed['port']) {
            $this->port = (int)$parsed['port'];
        }
        if (array_key_exists('path', $parsed) && '' !== $parsed['path']) {
            $this->path = (string)$parsed['path'];
        }
        if (array_key_exists('query', $parsed) && '' !== $parsed['query']) {
            $this->query = (string)$parsed['query'];
        }
        if (array_key_exists('fragment', $parsed) && '' !== $parsed['fragment']) {
            $this->fragment = (string)$parsed['fragment'];
        }
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param string $protocol
     *
     * @return RequestBuilder
     */
    public function protocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return RequestBuilder
     */
    public function text()
    {
        return $this->contentType('text/html');
    }

    /**
     * @return RequestBuilder
     */
    public function json()
    {
        return $this->contentType('application/json');
    }

    /**
     * @return RequestBuilder
     */
    public function noprocess()
    {
        $this->noProcess = true;

        return $this;
    }

    /**
     * @return RequestBuilder
     */
    public function xml()
    {
        return $this->contentType('application/xml');
    }

    /**
     * @return RequestBuilder
     */
    public function encoded()
    {
        return $this->contentType('application/x-www-form-urlencoded');
    }

    /**
     * @return RequestBuilder
     */
    public function formData()
    {
        return $this->contentType('multipart/form-data');
    }

    /**
     * @param string $encoding
     *
     * @return RequestBuilder
     */
    public function encoding($encoding)
    {
        return $this->header('Content-Encoding', $encoding);
    }

    /**
     * @param string $path
     *
     * @return RequestBuilder
     */
    public function path($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return RequestBuilder
     */
    public function query($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $fragment
     *
     * @return RequestBuilder
     */
    public function fragment($fragment)
    {
        $this->fragment = $fragment;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return RequestBuilder
     * @deprecated use RequestBuilder::uri
     */
    public function host($host)
    {
        return $this->uri($host);
    }
    /**
     * @param int $port
     *
     * @return RequestBuilder
     */
    public function port($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return RequestBuilder
     */
    public function nossl()
    {
        $this->ssl = false;

        return $this;
    }

    /**
     * @param bool $return
     *
     * @return RequestBuilder
     */
    public function responseHeaders($return = false)
    {
        $this->returnHeaders = $return;

        return $this;
    }

    /**
     * @param string $referer
     *
     * @return RequestBuilder
     */
    public function referer($referer)
    {
        return $this->header('Referer', $referer);
    }

    /**
     * @param string $userAgent
     *
     * @return RequestBuilder
     */
    public function userAgent($userAgent)
    {
        return $this->header('User-Agent', $userAgent);
    }

    /**
     *
     */
    public function clean()
    {
        $this->method = $this->user = $this->password = $this->scheme =
        $this->host = $this->port = $this->path = $this->query = $this->fragment = $this->body = null;
        $this->parameters = $this->headers = $this->options = [];
        $this->ssl = true;
        $this->returnHeaders = $this->noProcess = false;
        $this->authType = $this->connectMs = $this->timeoutMs = 0;
        $this->protocol = '1.1';
    }

    /**
     * @return CurlRequest
     */
    public function build()
    {
        if (null === $this->method) {
            throw new \RuntimeException('Request must be defined with method');
        }
        if (null === $this->host) {
            throw new \RuntimeException('Request must be defined with host');
        }
        $matches = [];
        $count = preg_match_all('/\{([a-zA-Z0-9\-\_]+)\}/', $this->path, $matches);
        if (false === $count) {
            throw new \RuntimeException(sprintf('Cannot check placeholders in path %s', $this->path));
        }
        if (0 !== $count) {
            $parameters = [];
            if ([] !== $this->parameters) {
                $parameters = $this->parameters;
            }
            if (\is_array($this->body)) {
                $parameters = array_merge($this->parameters, $this->body);
            }
            if ([] === $parameters) {
                throw new \LogicException(
                    sprintf(
                        'Path %s has placeholders but you didn\'t specify neither parameters nor appropriate body',
                        $this->path
                    )
                );
            }
            $search = [];
            $replacement = [];
            foreach (array_unique($matches[1]) as $placeHolder) {
                if (false === array_key_exists($placeHolder, $parameters)) {
                    throw new \LogicException(
                        sprintf(
                            'Path %s has placeholder {%s} but neither parameters nor body have value for that',
                            $this->path,
                            $placeHolder
                        )
                    );
                }
                $search[] = sprintf('{%s}', $placeHolder);
                $replacement[] = $parameters[$placeHolder];
            }
            $this->path = str_replace($search, $replacement, $this->path);
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
                        if (JSON_ERROR_NONE !== json_last_error()) {
                            $message = json_last_error_msg();
                            json_encode(null);
                            throw new \RuntimeException(sprintf('Failed to json_encode body. Error: %s', $message));
                        }
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
                    $this->query = http_build_query($body);
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
        if ($this->port) {
            $uri .= ':' . $this->port;
        }
        if ($this->path) {
            $uri .= '/' . $this->path;
        }
        if ($this->query) {
            $uri .= '?' . $this->query;
        }
        if ($this->fragment) {
            $uri .= '#' . $this->fragment;
        }
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
