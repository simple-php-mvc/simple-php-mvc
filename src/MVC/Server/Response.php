<?php

/**
 * The Response class is used to render an HTTP response.
 *
 * @author    Ramon Serrano <ramon.calle.88@gmail.com>
 * @package MVC\Server
 */

namespace MVC\Server;

class Response
{

   /**
    * The configuration settings.
    * @access protected
    * @var array
    */
    protected $_config = array();

   /**
    * Sets the configuration options.
    *
    * Possible keys include the following:
    *
    * * 'buffer_size' - The number of bytes each chunk of output should contain
    *
    * @access public
    * @param array $config    The configuration options.
    */
    public function __construct(array $config = array()) 
    {
        $defaults = array(
            'buffer_size'  => 8192
        );
        $this->_config = $config + $defaults;
    }

   /**
    * Converts an integer status to a well-formed HTTP status header.
    * @access public
    * @param int $code    The integer associated with the HTTP status.
    * @return string
    */
    public function _convert_status($code)
    {
        $statuses = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Time-out',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Large',
            415 => 'Unsupported Media Type',
            416 => 'Requested range not satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Time-out'
        );
        if ( isset($statuses[$code]) ) {
            return "HTTP/1.1 {$code} {$statuses[$code]}";
        }
        return "HTTP/1.1 200 OK";
    }

   /**
    * Parses a response.
    * @access protected
    * @param mixed $response    The response to be parsed.  Can be an array
    *                           containing 'body', 'headers', and/or 'status'
    *                           keys, or a string which will be used as the
    *                           body of the response.  Note that the headers
    *                           must be well-formed HTTP headers, and the
    *                           status must be an integer (i.e., the one
    *                           associated with the HTTP status code).
    * @return array
    */
    protected function _parse($response)
    {
        $defaults = array(
            'body'    => '',
            'headers' => array('Content-Type: text/html; charset=utf-8'),
            'status'  => 200
        );
        if ( is_array($response) ) {
            $response += $defaults;
        } elseif ( is_string($response) ) {
            $defaults['body'] = $response;
            $response = $defaults;
        } else {
            throw new \LogicException('Response can\'t be NULL.');
        }
        return $response;
    }

    /**
     * Redirect
     *
     * This method immediately redirects to a new URL. By default,
     * this issues a 302 Found response; this is considered the default
     * generic redirect response. You may also specify another valid
     * 3xx status code if you want. This method will automatically set the
     * HTTP Location header for you using the URL parameter.
     * @access public
     * @param  string   $url        The destination URL
     * @param  int      $status     The HTTP redirect status code (optional)
     */
    public function redirect($url, $status = 302)
    {
        if (!headers_sent()) {
            header("Location: $url", true, $status);
        }
    }


   /**
    * Renders a response.
    * @access public
    * @param mixed $response    The response to be rendered.  Can be an array
    *                           containing 'body', 'headers', and/or 'status'
    *                           keys, or a string which will be used as the
    *                           body of the response.  Note that the headers
    *                           must be well-formed HTTP headers, and the
    *                           status must be an integer (i.e., the one
    *                           associated with the HTTP status code).  The
    *                           response body is chunked according to the
    *                           buffer_size set in the constructor.
    * @param int $status        The status of the response
    * @return void
    */
    public function render($response, $status = null)
    {
        $response = $this->_parse($response);

        if (!is_null($status)) {
            $status = $this->_convert_status($status);
        } elseif (!is_null($response['status'])) {
            $status = $this->_convert_status($response['status']);
        } else {
            $status = $this->_convert_status(500);
        }
        
        if (!headers_sent()) {
            if (!strpos(PHP_SAPI, 'cgi')) {
                header($status);
            }
            foreach ( $response['headers'] as $header ) {
                header($header, false);
            }
        }

        $buffer_size = $this->_config['buffer_size'];
        $length = strlen($response['body']);
        for ( $i = 0; $i < $length; $i += $buffer_size ) {
            echo substr($response['body'], $i, $buffer_size);
        }
    }

}