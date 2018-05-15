<?php
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

    /**
     * @param string $username
     * @param string $password
     * @param string $type
     *
     * @return RequestBuilder
     */
    public function auth($username, $password, $type = '')
    {
        $this->curl(CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));
        if ('' !== $type) {
            $this->curl(CURLOPT_HTTPAUTH, $type);
        }

        return $this;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return RequestBuilder
     */
    public function user($username, $password)
    {
        return $this->curl(CURLOPT_USERPWD, sprintf('%s:%s', $username, $password));
    }

    /**
     * @param string $authType
     *
     * @return RequestBuilder
     */
    public function authType($authType)
    {
        return $this->curl(CURLOPT_HTTPAUTH, $authType);
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
        $this->options[CURLOPT_TIMEOUT_MS] = $msec;

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
        $this->options[CURLOPT_CONNECTTIMEOUT_MS] = $msec;

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
        $this->headers = $headers;

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
        $this->options = $options;

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
        $this->uri = $uri;
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
        return $this->curl(CURLOPT_ENCODING, $encoding);
    }

    /**
     * @param int $port
     *
     * @return RequestBuilder
     */
    public function port($port)
    {
        return $this->curl(CURLOPT_PORT, $port);
    }

    /**
     * @return RequestBuilder
     */
    public function nossl()
    {
        return $this
            ->curl(CURLOPT_SSL_VERIFYPEER, false)
            ->curl(CURLOPT_SSL_VERIFYHOST, false);
    }

    /**
     * @param bool $return
     *
     * @return RequestBuilder
     */
    public function responseHeaders($return = false)
    {
        return $this->curl(CURLOPT_HEADER, $return);
    }

    /**
     * @param string $referer
     *
     * @return RequestBuilder
     */
    public function referer($referer)
    {
        return $this->curl(CURLOPT_REFERER, $referer);
    }

    /**
     * @param string $userAgent
     *
     * @return RequestBuilder
     */
    public function userAgent($userAgent)
    {
        return $this->curl(CURLOPT_USERAGENT, $userAgent);
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
        $uri = $this->uri;
        preg_match_all('/\{([a-zA-Z0-9\-\_]+)\}/', $uri, $matches);
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
