<?php

namespace BharatPHP;

use Exception;
use BharatPHP\Cookie;

class Response
{

    /**
     * Response codes & messages
     * @var array
     */
    protected static $responseCodes = [
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * HTTP version
     * @var string
     */
    protected $version = '1.1';

    /**
     * Response code
     * @var int
     */
    protected $code = null;

    /**
     * Response message
     * @var string
     */
    protected $message = null;

    /**
     * Response headers
     * @var array
     */
    protected $headers = [];

    /**
     * Response body
     * @var string
     */
    protected $body = null;

    /**
     * Constructor
     *
     * Instantiate the response object
     *
     * @param  array $config
     * @throws Exception
     * @return Response
     */
    public function __construct(array $config = [])
    {
        $this->setCode((isset($config['code'])) ? $config['code'] : 200);
        $this->setBody((isset($config['body'])) ? $config['body'] : null);
        $this->setMessage((isset($config['message'])) ? $config['message'] : self::$responseCodes[$this->code]);
        $this->setVersion((isset($config['version'])) ? $config['version'] : '1.1');

        $headers = (isset($config['headers']) && is_array($config['headers'])) ?
            $config['headers'] : ['Content-Type' => 'text/html'];

        $this->setHeaders($headers);
    }

    /**
     * Send redirect
     *
     * @param  string $url
     * @param  string $code
     * @param  string $version
     * @throws Exception
     * @return void
     */
    public  function redirect($url, $code = '302', $version = '1.1')
    {
        if (headers_sent()) {
            throw new Exception('The headers have already been sent.');
        }

        if (!array_key_exists($code, self::$responseCodes)) {
            throw new Exception('The header code ' . $code . ' is not allowed.');
        }

        $this->setCode($code);
        $this->setHeader("Location",$url);
        // header("HTTP/{$version} {$code} " . self::$responseCodes[$code]);
        // header("Location: {$url}");
    }

    /**
     * Send redirect and exit
     *
     * @param  string  $url
     * @param  string  $code
     * @param  string  $version
     * @return void
     */
    public static function redirectAndExit($url, $code = '302', $version = '1.1')
    {

        static::redirect($url, $code, $version);
        exit();
    }

    /**
     * THis function to be removed session save should happen in Application.pphp
     */
    public static function redirectAndExitAndSaveSession($url, $code = '302', $version = '1.1')
    {
        Session::save();
        static::redirect($url, $code, $version);
        exit();
    }

    /**
     * Get response message from code
     *
     * @param  int $code
     * @throws Exception
     * @return string
     */
    public static function getMessageFromCode($code)
    {
        if (!array_key_exists($code, self::$responseCodes)) {
            throw new Exception('The header code ' . $code . ' is not valid.');
        }

        return self::$responseCodes[$code];
    }

    /**
     * Encode the body data
     *
     * @param  string $body
     * @param  string $encode
     * @throws Exception
     * @return string
     */
    public static function encodeBody($body, $encode = 'gzip')
    {
        switch ($encode) {
                // GZIP compression
            case 'gzip':
                if (!function_exists('gzencode')) {
                    throw new Exception('Gzip compression is not available.');
                }
                $encodedBody = gzencode($body);
                break;

                // Deflate compression
            case 'deflate':
                if (!function_exists('gzdeflate')) {
                    throw new Exception('Deflate compression is not available.');
                }
                $encodedBody = gzdeflate($body);
                break;

                // Unknown compression
            default:
                $encodedBody = $body;
        }

        return $encodedBody;
    }

    /**
     * Decode the body data
     *
     * @param  string $body
     * @param  string $decode
     * @throws Exception
     * @return string
     */
    public static function decodeBody($body, $decode = 'gzip')
    {
        switch ($decode) {
                // GZIP compression
            case 'gzip':
                if (!function_exists('gzinflate')) {
                    throw new Exception('Gzip compression is not available.');
                }
                $decodedBody = gzinflate(substr($body, 10));
                break;

                // Deflate compression
            case 'deflate':
                if (!function_exists('gzinflate')) {
                    throw new Exception('Deflate compression is not available.');
                }
                $zlibHeader = unpack('n', substr($body, 0, 2));
                $decodedBody = ($zlibHeader[1] % 31 == 0) ? gzuncompress($body) : gzinflate($body);
                break;

                // Unknown compression
            default:
                $decodedBody = $body;
        }

        return $decodedBody;
    }

    /**
     * Decode a chunked transfer-encoded body and return the decoded text
     *
     * @param string $body
     * @return string
     */
    public static function decodeChunkedBody($body)
    {
        $decoded = '';

        while ($body != '') {
            $lfPos = strpos($body, "\012");

            if ($lfPos === false) {
                $decoded .= $body;
                break;
            }

            $chunkHex = trim(substr($body, 0, $lfPos));
            $scPos = strpos($chunkHex, ';');

            if ($scPos !== false) {
                $chunkHex = substr($chunkHex, 0, $scPos);
            }

            if ($chunkHex == '') {
                $decoded .= substr($body, 0, $lfPos);
                $body = substr($body, $lfPos + 1);
                continue;
            }

            $chunkLength = hexdec($chunkHex);

            if ($chunkLength) {
                $decoded .= substr($body, $lfPos + 1, $chunkLength);
                $body = substr($body, $lfPos + 2 + $chunkLength);
            } else {
                $body = '';
            }
        }

        return $decoded;
    }

    /**
     * Determine if the response is a success
     *
     * @return boolean
     */
    public function isSuccess()
    {
        $type = floor($this->code / 100);
        return (($type == 1) || ($type == 2) || ($type == 3));
    }

    /**
     * Determine if the response is a redirect
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $type = floor($this->code / 100);
        return ($type == 3);
    }

    /**
     * Determine if the response is an error
     *
     * @return boolean
     */
    public function isError()
    {
        $type = floor($this->code / 100);
        return (($type == 4) || ($type == 5));
    }

    /**
     * Determine if the response is a client error
     *
     * @return boolean
     */
    public function isClientError()
    {
        $type = floor($this->code / 100);
        return ($type == 4);
    }

    /**
     * Determine if the response is a server error
     *
     * @return boolean
     */
    public function isServerError()
    {
        $type = floor($this->code / 100);
        return ($type == 5);
    }

    /**
     * Get the response version
     *
     * @return float
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get the response code
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the response message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the response header
     *
     * @param  string $name
     * @return string
     */
    public function getHeader($name)
    {
        return (isset($this->headers[$name])) ? $this->headers[$name] : null;
    }

    /**
     * Get the response headers as a string
     *
     * @param  boolean $status
     * @param  string  $eol
     * @return string
     */
    public function getHeadersAsString($status = true, $eol = "\n")
    {
        $headers = '';

        if ($status) {
            $headers = "HTTP/{$this->version} {$this->code} {$this->message}{$eol}";
        }

        foreach ($this->headers as $name => $value) {
            $headers .= "{$name}: {$value}{$eol}";
        }

        return $headers;
    }

    /**
     * Set the response version
     *
     * @param  float $version
     * @return Response
     */
    public function setVersion($version = 1.1)
    {
        if (($version == 1.0) || ($version == 1.1)) {
            $this->version = $version;
        }
        return $this;
    }

    /**
     * Set the response code
     *
     * @param  int $code
     * @throws Exception
     * @return Response
     */
    public function setCode($code = 200)
    {
        if (!array_key_exists($code, self::$responseCodes)) {
            throw new Exception('That header code ' . $code . ' is not allowed.');
        }

        $this->code = $code;
        $this->message = self::$responseCodes[$code];

        return $this;
    }

    /**
     * Set the response message
     *
     * @param  string $message
     * @return Response
     */
    public function setMessage($message = null)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Set the response body
     *
     * @param  string $body
     * @return Response
     */
    public function setBody($body = null)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set a response header
     *
     * @param  string $name
     * @param  string $value
     * @throws Exception
     * @return Response
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set response headers
     *
     * @param  array $headers
     * @throws Exception
     * @return Response
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->headers[$name] = $value;
        }

        return $this;
    }

    /**
     * Set SSL headers to fix file cache issues over SSL in certain browsers.
     *
     * @return Response
     */
    public function setSslHeaders()
    {
        $this->headers['Expires'] = 0;
        $this->headers['Cache-Control'] = 'private, must-revalidate';
        $this->headers['Pragma'] = 'cache';

        return $this;
    }

    /**
     * Send headers
     *
     * @throws Exception
     * @return void
     */
    public function sendHeaders()
    {
        header("HTTP/{$this->version} {$this->code} {$this->message}");
        foreach ($this->headers as $name => $value) {
            header($name . ": " . $value);
        }
    }

    /**
     * Send response
     *
     * @param  int   $code
     * @param  array $headers
     * @throws Exception
     * @return void
     */
    public function send($code = null, array $headers = null)
    {

        if (headers_sent()) {
            throw new Exception('The headers have already been sent.');
        }

        if (null !== $code) {
            $this->setCode($code);
        }
        if (null !== $headers) {
            $this->setHeaders($headers);
        }

        // cookies
        if (!empty(Cookie::$jar)) {

            foreach (Cookie::$jar as $cookie) {
                //                setcookie($cookie['name'], $cookie['value'], $cookie->getExpiresTime(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());

                if (is_null($cookie['secure'])) {
                    $cookie['secure'] = false;
                }
                if (is_null($cookie['domain'])) {
                    $cookie['domain'] = '';
                }
                if(is_null($cookie['value'])){
                    $cookie['value']='';
                }
                setcookie($cookie['name'], $cookie['value'], $cookie['expiration'], $cookie['path'], $cookie['domain'], $cookie['secure'],$cookie['httponly']);
            }
        }



        $body = $this->body;

        if (array_key_exists('Content-Encoding', $this->headers)) {
            $body = self::encodeBody($body, $this->headers['Content-Encoding']);
            $this->headers['Content-Length'] = strlen($body);
        }

        $this->sendHeaders();

        echo $body;
    }

    /**
     * Return entire response as a string
     *
     * @return string
     */
    public function __toString()
    {
        $body = $this->body;

        if (array_key_exists('Content-Encoding', $this->headers)) {
            $body = self::encodeBody($body, $this->headers['Content-Encoding']);
            $this->headers['Content-Length'] = strlen($body);
        }

        return $this->getHeadersAsString() . "\n" . $body;
    }
}
