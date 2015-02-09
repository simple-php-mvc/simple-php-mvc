<?php

/**
 * Description of HttpRequest
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC\Server
 */

namespace MVC\Server;

class HttpRequest
{
    
    /**
     * The POST/PUT/DELETE data.
     * @access public
     * @var \stdClass
     */
    public $data;

    /**
     * The environment variables.
     * @access protected
     * @var array
     */
    protected $_env = array();
    
    /**
     * HttpRequest Instance
     * 
     * @var HttpRequest
     */
    static $instance;

    /**
     * The GET data.
     * @access public
     * @var \stdClass
     */
    public $query;
    
    /**
     * The request Method
     * 
     * @access public
     * @var string
     */
    public $method = 'GET';

    /**
     * The parameters parsed from the request url.
     * @access public
     * @var array
     */
    public $params;

    /**
     * The url of the request.
     * @access public
     * @var string
     */
    public $url;

    /**
     * Construct of the class
     */
    function __construct()
    {
        $this->data = new \stdClass();
        $this->query = new \stdClass();
        $this->params = array();
        
        $this->_env = $_SERVER + $_ENV + array(
            "CONTENT_TYPE"   => "text/html",
            "CONTENT_LENGTH" => 0,
            "REQUEST_METHOD" => "GET"
        );
        
        if (!isset($this->_env['REQUEST_URI'])) {
            $this->_env['REQUEST_URI'] = '';
        }
        
        if ( isset($this->_env["SCRIPT_URI"]) ) {
            $this->_env["HTTPS"] =
                ( strpos($this->_env["SCRIPT_URI"], "https://") === 0 );
        } elseif ( isset($this->_env["HTTPS"]) ) {
            $this->_env["HTTPS"] = (
                !empty($this->_env["HTTPS"]) && $this->_env["HTTPS"] !== "off"
            );
        } else {
            $this->_env["HTTPS"] = false;
        }
        
        $parsed = $this->parseUrl();
        
        if (!isset($this->_env['BASE'])) {
            $base = "/" . ltrim(
                str_replace("\\", "/", dirname($this->_env["PHP_SELF"])),
            "/");
        } else {
            $base = $this->_env['BASE'];
        }
        
        $pattern = "/^" . preg_quote($base, "/") . "/";
        $this->url = "/" . trim(
            preg_replace($pattern, "", $parsed['path']),
        "/");
        
        if ( isset($parsed["query"]) ) {
            $query_string = str_replace("%20", "+", $parsed["query"]);
            $pairs = explode("&", $query_string);            
            foreach ( $pairs as $pair ) {
                list($k, $v) = array_map("urldecode", explode("=", $pair));
                $this->query->$k = $v;
            }
        }
        
        if ( isset($_POST) ) {
            foreach ($_POST as $key => $value) {
                $this->data->$key = $value;
            }            
        }
        
        if($data_json = @file_get_contents("php://input")){
            $this->data->JSON = $data_json;
        }
        
        $override = "HTTP_X_HTTP_METHOD_OVERRIDE";
        if ( isset($this->data->_method) ) {
            $this->_env[$override] = strtoupper($this->data->_method);
            unset($this->data->_method);
        }
        if ( !empty($this->_env[$override]) ) {
            $this->_env["REQUEST_METHOD"] = $this->_env[$override];
        }
        
        $this->method = strtoupper($this->_env["REQUEST_METHOD"]);
        
        if ( $this->method == "PUT" || $this->method == "DELETE" ) {
            $stream = fopen("php://input", "r");
            parse_str(stream_get_contents($stream), $this->data);
            fclose($stream);
        }
    }
    
    /**
     * Returns an environment variable.
     * 
     * @access public
     * @param string $key    The environment variable.
     * @return mixed
     */
    public function __get($key)
    {
        $key = strtoupper($key);
        return ( isset($this->_env[$key]) ) ? $this->_env[$key] : null;
    }
    
    /**
     * Set key env value
     * 
     * @param string $key
     * @param mixed $value
     * @return HttpRequest
     */
    public function __set($key, $value)
    {
        $key = strtoupper($key);
        $this->_env[$key] = $value;
        
        return $this;
    }
    
    /**
     * Get HttpRequest Instance
     * 
     * @return HttpRequest
     */
    static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Get Request HTTP Method
     * 
     * @access public
     * @return string Request HTTP Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Retrieve HTTP Body
     * @access public
     * @return string JSON
     */
    public function getBody()
    {
        return $this->data->JSON;
    }

    /**
     * Returns the Root URI
     * @access public
     * @return string
     */
    public function getRootUri()
    {
        return $this->__get('SCRIPT_NAME');
    }
    
    /**
     * Returns the Request URI
     * @access public
     * @return string
     */
    public function getRequestUri()
    {
        return $this->__get('REQUEST_URI');
    }
    
    /**
     * Checks for request characteristics.
     *
     * The full list of request characteristics is as follows:
     *
     * * "ajax" - XHR
     * * "delete" - DELETE REQUEST_METHOD
     * * "flash" - "Shockwave Flash" HTTP_USER_AGENT
     * * "get" - GET REQUEST_METHOD
     * * "head" - HEAD REQUEST_METHOD
     * * "mobile"  - any one of the following HTTP_USER_AGENTS:
     *
     * 1. "Android"
     * 1. "AvantGo"
     * 1. "Blackberry"
     * 1. "DoCoMo"
     * 1. "iPod"
     * 1. "iPhone"
     * 1. "J2ME"
     * 1. "NetFront"
     * 1. "Nokia"
     * 1. "MIDP"
     * 1. "Opera Mini"
     * 1. "PalmOS"
     * 1. "PalmSource"
     * 1. "Plucker"
     * 1. "portalmmm"
     * 1. "ReqwirelessWeb"
     * 1. "SonyEricsson"
     * 1. "Symbian"
     * 1. "UP.Browser"
     * 1. "Windows CE"
     * 1. "Xiino"
     *
     * * "options" - OPTIONS REQUEST_METHOD
     * * "post"    - POST REQUEST_METHOD
     * * "put"     - PUT REQUEST_METHOD
     * * "ssl"     - HTTPS
     *
     * @access public
     * @param string $characteristic    The characteristic.
     * @return bool
     */
    public function is($characteristic)
    {
        switch ( strtolower($characteristic) ) {
            case "ajax":
                return ( 
                    isset($this->_env['HTTP_X_REQUESTED_WITH']) &&
                    $this->_env['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest"
                );
            case "delete":
                return ( $this->method == "DELETE" );
            case "flash":
                return (
                    $this->_env['HTTP_USER_AGENT'] == "Shockwave Flash"
                );
            case "get":
                return ( $this->method == "GET" );
            case "head":
                return ( $this->method == "HEAD" );
            case "mobile":
                $mobile_user_agents = array(
                    "Android", "AvantGo", "Blackberry", "DoCoMo", "iPod",
                    "iPhone", "J2ME", "NetFront", "Nokia", "MIDP", "Opera Mini",
                    "PalmOS", "PalmSource", "Plucker", "portalmmm",
                    "ReqwirelessWeb", "SonyEricsson", "Symbian", "UP\.Browser",
                    "Windows CE", "Xiino"
                );
                $pattern = "/" . implode("|", $mobile_user_agents) . "/i";
                return (boolean) preg_match(
                    $pattern, $this->_env['HTTP_USER_AGENT']
                );
            case "options":
                return ( $this->method == "OPTIONS" );
            case "post":
                return ( $this->method == "POST" );
            case "put":
                return ( $this->method == "PUT" );
            case "ssl":
                return $this->_env['HTTPS'];
            default:
                return false;
        }
    }
    
    /**
     * Is AJAX
     * 
     * @return boolean
     */
    public function isAjax()
    {
        return $this->is('ajax');
    }
    
    /**
     * Is DELETE
     * 
     * @return boolean
     */
    public function isDelete()
    {
        return $this->is('delete');
    }
    
    /**
     * Is HEAD
     * 
     * @return boolean
     */
    public function isHead()
    {
        return $this->is('head');
    }
    
    /**
     * Is GET
     * 
     * @return boolean
     */
    public function isGet()
    {
        return $this->is('get');
    }
    
    /**
     * Is OPTIONS
     * 
     * @return boolean
     */
    public function isOptions()
    {
        return $this->is('options');
    }
    
    /**
     * Is POST
     * 
     * @return boolean
     */
    public function isPost()
    {
        return $this->is('post');
    }
    
    /**
     * Is PUT
     * 
     * @return boolean
     */
    public function isPut()
    {
        return $this->is('put');
    }
    
    /**
     * Get parsed url
     * 
     * @return array Parsed Url
     */
    protected function parseUrl()
    {
        $parsed = parse_url($this->_env["REQUEST_URI"]);
        
        if (preg_match('/[a-zA-Z0-9_]+\.php/i', $parsed['path'], $matches)) {
            $parsed['path'] = preg_replace("/$matches[0]/", '/', $parsed['path']);
        }
        
        return $parsed;
    }
    
    /**
     * Set Method
     * 
     * @param string $method
     * @return HttpRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;
        
        return $this;
    }

    /**
     * Set Url
     * 
     * @param string $url
     * @return HttpRequest
     */
    public function setUrl($url)
    {
        $this->url = $url;
        
        return $this;
    }
}
