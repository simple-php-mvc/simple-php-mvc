<?php
/**
 * Description of Session
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC
 */

namespace MVC\Sessions;

class Session 
{
    
    /**
     * Instance of Session
     * @access public
     * @var Session
     */
    static $instance;

    /**
     * Sessions namespace
     *
     * @var string
     */
    static $namespace = 'SIMPLE_PHP_MVC';

    /**
     * Sessions vars
     *
     * @var array
     */
    private $vars = array();
    
    /**
     * Construct of the class
     */
    public function __construct()
    {
        $this->__init();
        $_SESSION[self::$namespace] = array();
        $this->vars = &$_SESSION[self::$namespace];
    }

    /**
     * Sessions destroy
     * @access public
     * @return void
     */
    public static function __destroy()
    {
        session_destroy();
    }

    /**
     * Start sessions vars
     * @access public
     * @return void
     */
    public static function __init()
    {
        session_start();
    }

    /**
     * Get the value of Session key
     * @access public
     * @param $name
     * @return bool|mixed
     */
    public  function get($name)
    {
        if ($this->has($name)) {
            return $this->vars[$name];
        } else {
            return false;
        }
    }
    
    /**
     * Gets the instance of Session
     * @access public
     * @return Session
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Has session name
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->vars[$name]);
    }
    
    /**
     * Set var session
     * @access public
     * @param string $name
     * @param mixed $value
     * @return bool
     */
    public function set($name, $value)
    {
        if (!$this->has($name)) {
            $this->vars[$name] = $value;
        } else {
            return false;
        }
    }

}
