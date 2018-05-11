<?php
declare(strict_types=1);

namespace Http\Client\Curl;

class CurlInfo implements \ArrayAccess, \Countable, \IteratorAggregate
{
    const CURL_URL = 'url';
    const CURL_CONTENT_TYPE = 'content_type';
    const CURL_HTTP_CODE = 'http_code';
    const CURL_HEADER_SIZE = 'header_size';
    const CURL_REQUEST_SIZE = 'request_size';
    const CURL_FILE_TIME = 'filetime';
    const CURL_SSL_VERIFY_RESULT = 'ssl_verify_result';
    const CURL_REDIRECT_COUNT = 'redirect_count';
    const CURL_TOTAL_TIME = 'total_time';
    const CURL_NAMELOOKUP_TIME = 'namelookup_time';
    const CURL_CONNECT_TIME = 'connect_time';
    const CURL_PRETRANSFER_TIME = 'pretransfer_time';
    const CURL_SIZE_UPLOAD = 'size_upload';
    const CURL_SIZE_DOWNLOAD = 'size_download';
    const CURL_SPEED_DOWNLOAD = 'speed_download';
    const CURL_SPEED_UPLOAD = 'speed_upload';
    const CURL_DOWNLOAD_CONTENT_LENGTH = 'download_content_length';
    const CURL_UPLOAD_CONTENT_LENGTH = 'upload_content_length';
    const CURL_STARTTRANSFER_TIME = 'starttransfer_time';
    const CURL_REDIRECT_TIME = 'redirect_time';
    const CURL_REDIRECT_URL = 'redirect_url';
    const CURL_PRIMARY_IP = 'primary_ip';
    const CURL_CERTINFO = 'certinfo';
    const CURL_PRIMARY_PORT = 'primary_port';
    const CURL_LOCAL_IP = 'local_ip';
    const CURL_LOCAL_PORT = 'local_port';

    private $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->info);
    }

    public function count()
    {
        return count($this->info);
    }

    public function get(string $key, $default = null)
    {
        if (false === $this->offsetExists($key)) {
            return $default;
        }

        return $this->offsetGet($key);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->info);
    }

    public function offsetGet($offset)
    {
        if (false === $this->offsetExists($offset)) {
            return null;
        }

        return $this->info[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->info[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (false === $this->offsetExists($offset)) {
            return;
        }
        unset($this->info[$offset]);
    }

    public function url(): ?string
    {
        return $this->get(self::CURL_URL);
    }

    public function contentType(): ?string
    {
        return $this->get(self::CURL_CONTENT_TYPE);
    }

    public function httpCode(): ?int
    {
        return $this->get(self::CURL_HTTP_CODE);
    }

    public function headerSize()
    {
        return $this->get(self::CURL_HEADER_SIZE);
    }

    public function requestSize()
    {
        return $this->get(self::CURL_REQUEST_SIZE);
    }

    public function fileTime(): float
    {
        return (float)$this->get(self::CURL_FILE_TIME);
    }

    public function sslVerifyResult()
    {
        return $this->get(self::CURL_SSL_VERIFY_RESULT);
    }

    public function redirectCount()
    {
        return $this->get(self::CURL_REDIRECT_COUNT);
    }

    public function totalTime() : float
    {
        return (float)$this->get(self::CURL_TOTAL_TIME);
    }

    public function namelookupTime(): float
    {
        return (float)$this->get(self::CURL_NAMELOOKUP_TIME);
    }

    public function connectTime(): float
    {
        return (float)$this->get(self::CURL_CONNECT_TIME);
    }

    public function pretranasferTime(): float
    {
        return (float)$this->get(self::CURL_PRETRANSFER_TIME);
    }

    public function sizeUpload()
    {
        return $this->get(self::CURL_SIZE_UPLOAD);
    }

    public function sizeDownload()
    {
        return $this->get(self::CURL_SIZE_DOWNLOAD);
    }

    public function speedDownload()
    {
        return $this->get(self::CURL_SPEED_DOWNLOAD);
    }

    public function speedUpload()
    {
        return $this->get(self::CURL_SPEED_UPLOAD);
    }

    public function downloadContentLength()
    {
        return $this->get(self::CURL_DOWNLOAD_CONTENT_LENGTH);
    }

    public function uploadContentLength()
    {
        return $this->get(self::CURL_UPLOAD_CONTENT_LENGTH);
    }

    public function starttransferTime(): float
    {
        return (float)$this->get(self::CURL_STARTTRANSFER_TIME);
    }

    public function redirectTime(): float
    {
        return (float)$this->get(self::CURL_REDIRECT_TIME);
    }

    public function redirectUrl(): ?string
    {
        return $this->get(self::CURL_REDIRECT_URL);
    }

    public function primaryIp(): string
    {
        return (string)$this->get(self::CURL_PRIMARY_IP);
    }

    public function certInfo()
    {
        return $this->get(self::CURL_CERTINFO);
    }

    public function primaryPort() : int
    {
        return (int)$this->get(self::CURL_PRIMARY_PORT);
    }

    public function localIp()
    {
        return $this->get(self::CURL_LOCAL_IP);
    }

    public function localPort()
    {
        return $this->get(self::CURL_LOCAL_PORT);
    }

}
