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

    private $uri;

    private $body;

    private $parameters;

    private $headers = [];

    private $options = [];

    private $authType = '';

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
     * @param string $authType
     *
     * @return RequestBuilder
     */
    public function auth($username, $password, $authType = '')
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
     * @param string $authType
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
        $this->uri = ltrim($uri, '/');
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
     * @param string $host
     *
     * @return RequestBuilder
     */
    public function host($host)
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
        $this->host = $this->port = $this->uri = $this->body = null;
        $this->parameters = $this->headers = $this->options = [];
        $this->authType = '';
        $this->ssl = true;
        $this->returnHeaders = $this->noProcess = false;
        $this->connectMs = $this->timeoutMs = 0;
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
