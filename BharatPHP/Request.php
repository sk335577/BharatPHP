<?php

namespace BharatPHP;

class Request {

    protected $requestUri = null;
    protected $segments = [];
    protected $basePath = null;
    protected $headers = [];
    protected $rawData = null;
    protected $parsedData = null;
    protected $get = [];
    protected $post = [];
    protected $files = [];
    protected $put = [];
    protected $patch = [];
    protected $delete = [];
    protected $cookie = [];
    protected $server = [];
    protected $env = [];
    private array $routeParams = [];

    public function __construct($uri = null, $basePath = null) {
        $this->setRequestUri($uri, $basePath);

        $this->get = (isset($_GET)) ? $_GET : [];
        $this->post = (isset($_POST)) ? $_POST : [];
        $this->files = (isset($_FILES)) ? $_FILES : [];
        $this->cookie = (isset($_COOKIE)) ? $_COOKIE : [];
        $this->server = (isset($_SERVER)) ? $_SERVER : [];
        $this->env = (isset($_ENV)) ? $_ENV : [];

        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->parseData();
        }

        // Get any possible request headers
        if (function_exists('getallheaders')) {
            $this->headers = getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $key = ucfirst(strtolower(str_replace('HTTP_', '', $key)));
                    if (strpos($key, '_') !== false) {
                        $ary = explode('_', $key);
                        foreach ($ary as $k => $v) {
                            $ary[$k] = ucfirst(strtolower($v));
                        }
                        $key = implode('-', $ary);
                    }
                    $this->headers[$key] = $value;
                }
            }
        }
    }

    public function getMethod() {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getMethodUpperCase() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function getUrl() {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function isGet() {
        return $this->getMethod() === 'get';
    }

    public function isPost() {
        return $this->getMethod() === 'post';
    }

    public function getBody() {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return string|array
     */
    public function input($key = null, $default = null) {
        $input = $this->getBody();

        return array_get($input, $key, $default);
    }

    /**
     * @param $params
     * @return self
     */
    public function setRouteParams($params) {
        $this->routeParams = $params;
        return $this;
    }

    public function getRouteParams() {
        return $this->routeParams;
    }

    public function getRouteParam($param, $default = null) {
        return $this->routeParams[$param] ?? $default;
    }

    public function hasFiles() {
        return (count($this->files) > 0);
    }

    // public function isGet()
    // {
    //     return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'GET'));
    // }

    public function isHead() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'HEAD'));
    }

    // public function isPost()
    // {
    //     return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'POST'));
    // }

    public function isPut() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'PUT'));
    }

    public function isDelete() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'DELETE'));
    }

    public function isTrace() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'TRACE'));
    }

    public function isOptions() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'OPTIONS'));
    }

    public function isConnect() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'CONNECT'));
    }

    public function isPatch() {
        return (isset($this->server['REQUEST_METHOD']) && ($this->server['REQUEST_METHOD'] == 'PATCH'));
    }

    public function isSecure() {


        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $isSecure = true;
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        return $isSecure;

//        return (isset($this->server['HTTPS']) || (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')));
    }

    public function getBasePath() {
        return $this->basePath;
    }

    public function getRequestUri() {
        return $this->requestUri;
    }

    public function getFullRequestUri() {
        return $this->basePath . $this->requestUri;
    }

    public function getSegment($i) {
        return (isset($this->segments[(int) $i])) ? $this->segments[(int) $i] : null;
    }

    public function getSegments() {
        return $this->segments;
    }

    public function getDocumentRoot() {
        return (isset($this->server['DOCUMENT_ROOT'])) ? $this->server['DOCUMENT_ROOT'] : null;
    }

    // public function getMethod()
    // {
    //     return (isset($this->server['REQUEST_METHOD'])) ? $this->server['REQUEST_METHOD'] : null;
    // }

    public function getPort() {
        return (isset($this->server['SERVER_PORT'])) ? $this->server['SERVER_PORT'] : null;
    }

    public function getScheme() {
        return ($this->isSecure()) ? 'https' : 'http';
    }

    public function getHost() {
        $hostname = null;

        if (!empty($this->server['HTTP_HOST'])) {
            $hostname = $this->server['HTTP_HOST'];
        } else if (!empty($this->server['SERVER_NAME'])) {
            $hostname = $this->server['SERVER_NAME'];
        }

        if (strpos($hostname, ':') !== false) {
            $hostname = substr($hostname, 0, strpos($hostname, ':'));
        }

        return $hostname;
    }

    public function getFullHost() {
        $port = $this->getPort();
        $hostname = null;

        if (!empty($this->server['HTTP_HOST'])) {
            $hostname = $this->server['HTTP_HOST'];
        } else if (!empty($this->server['SERVER_NAME'])) {
            $hostname = $this->server['SERVER_NAME'];
        }

        if ((strpos($hostname, ':') === false) && (null !== $port)) {
            $hostname .= ':' . $port;
        }

        return $hostname;
    }

    public function getBaseUrl() {
        return $this->getScheme() . '://' . $this->getHost();
    }

//    public function appUrl() {
//        if (isset($_SERVER['HTTPS'])) {
//            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
//        } else {
//            $protocol = 'http';
//        }
//        // return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//        return $protocol . "://" . $_SERVER['HTTP_HOST'];
//    }

    public function appUrl() {
//        if (isset($_SERVER['HTTPS'])) {
//            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
//        } else {
//            $protocol = 'http';
//        }
        if ($this->isSecure()) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }
        // return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }

    public function getIp($proxy = true) {
        $ip = null;

        if ($proxy && isset($this->server['HTTP_CLIENT_IP'])) {
            $ip = $this->server['HTTP_CLIENT_IP'];
        } else if ($proxy && isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ip = $this->server['HTTP_X_FORWARDED_FOR'];
        } else if (isset($this->server['REMOTE_ADDR'])) {
            $ip = $this->server['REMOTE_ADDR'];
        }

        return $ip;
    }

    public function getQuery($key = null) {
        if (null === $key) {
            return $this->get;
        } else {
            return (isset($this->get[$key])) ? $this->get[$key] : null;
        }
    }

    public function getPost($key = null) {
        if (null === $key) {
            return $this->post;
        } else {
            return (isset($this->post[$key])) ? $this->post[$key] : null;
        }
    }

    public function getFiles($key = null) {
        if (null === $key) {
            return $this->files;
        } else {
            return (isset($this->files[$key])) ? $this->files[$key] : null;
        }
    }

    public function getPut($key = null) {
        if (null === $key) {
            return $this->put;
        } else {
            return (isset($this->put[$key])) ? $this->put[$key] : null;
        }
    }

    public function getPatch($key = null) {
        if (null === $key) {
            return $this->patch;
        } else {
            return (isset($this->patch[$key])) ? $this->patch[$key] : null;
        }
    }

    public function getDelete($key = null) {
        if (null === $key) {
            return $this->delete;
        } else {
            return (isset($this->delete[$key])) ? $this->delete[$key] : null;
        }
    }

    public function getCookie($key = null) {
        if (null === $key) {
            return $this->cookie;
        } else {
            return (isset($this->cookie[$key])) ? $this->cookie[$key] : null;
        }
    }

    public function getServer($key = null) {
        if (null === $key) {
            return $this->server;
        } else {
            return (isset($this->server[$key])) ? $this->server[$key] : null;
        }
    }

    public function getEnv($key = null) {
        if (null === $key) {
            return $this->env;
        } else {
            return (isset($this->env[$key])) ? $this->env[$key] : null;
        }
    }

    public function getParsedData($key = null) {
        $result = null;

        if ((null !== $this->parsedData) && is_array($this->parsedData)) {
            if (null === $key) {
                $result = $this->parsedData;
            } else {
                $result = (isset($this->parsedData[$key])) ? $this->parsedData[$key] : null;
            }
        }

        return $result;
    }

    public function getHeader($key) {
        return (isset($this->headers[$key])) ? $this->headers[$key] : null;
    }

    public function getHeaders() {
        return $this->headers;
    }

    public function getRawData() {
        return $this->rawData;
    }

    public function setBasePath($path = null) {
        $this->basePath = $path;
        return $this;
    }

    public function setRequestUri($uri = null, $basePath = null) {
        if ((null === $uri) && isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        }

        if (!empty($basePath)) {
            if (substr($uri, 0, (strlen($basePath) + 1)) == $basePath . '/') {
                $uri = substr($uri, (strpos($uri, $basePath) + strlen($basePath)));
            } else if (substr($uri, 0, (strlen($basePath) + 1)) == $basePath . '?') {
                $uri = '/' . substr($uri, (strpos($uri, $basePath) + strlen($basePath)));
            }
        }

        if (($uri == '') || ($uri == $basePath)) {
            $uri = '/';
        }

        // Some slash clean up
        $this->requestUri = $uri;
        $docRoot = (isset($_SERVER['DOCUMENT_ROOT'])) ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : null;
        $dir = str_replace('\\', '/', getcwd());

        if (($dir != $docRoot) && (strlen($dir) > strlen($docRoot))) {
            $realBasePath = str_replace($docRoot, '', $dir);
            if (substr($uri, 0, strlen($realBasePath)) == $realBasePath) {
                $this->requestUri = substr($uri, strlen($realBasePath));
            }
        }

        $this->basePath = (null === $basePath) ? str_replace($docRoot, '', $dir) : $basePath;

        if (strpos($this->requestUri, '?') !== false) {
            $this->requestUri = substr($this->requestUri, 0, strpos($this->requestUri, '?'));
        }

        if (($this->requestUri != '/') && (strpos($this->requestUri, '/') !== false)) {
            $uri = (substr($this->requestUri, 0, 1) == '/') ? substr($this->requestUri, 1) : $this->requestUri;
            $this->segments = explode('/', $uri);
        }

        return $this;
    }

    public function __get($name) {
        switch ($name) {
            case 'get':
                return $this->get;
                break;
            case 'post':
                return $this->post;
                break;
            case 'files':
                return $this->files;
                break;
            case 'put':
                return $this->put;
                break;
            case 'patch':
                return $this->patch;
                break;
            case 'delete':
                return $this->delete;
                break;
            case 'cookie':
                return $this->cookie;
                break;
            case 'server':
                return $this->server;
                break;
            case 'env':
                return $this->env;
                break;
            case 'parsed':
                return $this->parsedData;
                break;
            case 'raw':
                return $this->rawData;
                break;
            default:
                return null;
        }
    }

    protected function parseData() {
        if (strtoupper($this->getMethod()) == 'GET') {
            $this->rawData = (isset($_SERVER['QUERY_STRING'])) ? rawurldecode($_SERVER['QUERY_STRING']) : null;
        } else {
            $input = fopen('php://input', 'r');
            while ($data = fread($input, 1024)) {
                $this->rawData .= $data;
            }
        }

        // If the content-type is JSON
        if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'json') !== false)) {
            $this->parsedData = json_decode($this->rawData, true);
            // Else, if the content-type is XML
        } else if (isset($_SERVER['CONTENT_TYPE']) && (stripos($_SERVER['CONTENT_TYPE'], 'xml') !== false)) {
            $matches = [];
            preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $this->rawData, $matches);

            foreach ($matches[0] as $match) {
                $strip = str_replace(
                        ['<![CDATA[', ']]>', '<', '>'],
                        ['', '', '&lt;', '&gt;'],
                        $match
                );
                $this->rawData = str_replace($match, $strip, $this->rawData);
            }

            $this->parsedData = json_decode(json_encode((array) simplexml_load_string($this->rawData)), true);
            // Else, default to a regular URL-encoded string
        } else {
//            parse_str($this->rawData, $this->parsedData);
            if (!is_null($this->rawData)) {
                parse_str($this->rawData, $this->parsedData);
            }

            switch (strtoupper($this->getMethod())) {
                case 'GET':
                    $this->parsedData = $this->get;
                    break;

                case 'POST':
                    $this->parsedData = $this->post;
                    break;
            }
        }

        switch (strtoupper($this->getMethod())) {
            case 'PUT':
                $this->put = $this->parsedData;
                break;

            case 'PATCH':
                $this->patch = $this->parsedData;
                break;

            case 'DELETE':
                $this->delete = $this->parsedData;
                break;
        }
    }

}
