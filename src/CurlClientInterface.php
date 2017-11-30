<?php
namespace Http\Client\Curl;

use Http\Client\Curl\Response\CurlResponse;
use Http\Client\HttpClient as HttpClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Interface CurlClientInterface
 *
 * @method CurlResponse sendRequest(RequestInterface $request)
 */
interface CurlClientInterface extends HttpClientInterface
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

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setOption($option, $value);

    /**
     * @param int   $option
     * @param mixed $value
     *
     * @return CurlClientInterface
     */
    public function setRequestOption($option, $value);
}
