<?php

namespace MVC\Server;

/**
 * Description of Route
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class Route
{
    
    /**
     * Action Callback
     * 
     * @var string|\callable
     */
    protected $action;
    
    /**
     * Http Request Methods 
     * 
     * @var array
     */
    protected $methods = array('GET');
    
    /**
     * Route name
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Route URI Pattern
     * 
     * @var type 
     */
    protected $patternUri;
    
    /**
     * Route instance
     * 
     * @var Route
     */
    static $instance;
    
    /**
     * Valid Methods
     * 
     * @var array
     */
    static $validMethods = array(
        'ajax'    => 'XMLHttpRequest',
        'delete'  => 'DELETE',
        'get'     => 'GET', 
        'head'    => 'HEAD',
        'post'    => 'POST',
        'put'     => 'PUT',
        'options' => 'OPTIONS'
    );
    
    /**
     * Get Route instance
     * 
     * @param string|array $methods
     * @param string $patternUri
     * @param string|\callable $action
     * @param string $name
     */
    public function __construct($methods, $patternUri, $action, $name = null)
    {
        $this->setMethods($methods)
             ->setPatternUri($patternUri)
             ->setAction($action)
             ->setName($name);
    }
    
    /**
     * Get Route instance
     * 
     * @param string|array $methods
     * @param string $patternUri
     * @param string|\callable $action
     * @param string $name
     * @return Route
     */
    static function getInstance($methods, $patternUri, $action, $name = null)
    {
        if (!self::$instance) {
            self::$instance = new self($methods, $patternUri, $action, $name);
        }
        
        return self::$instance;
    }

    /**
     * Get action
     * 
     * @return string|callable
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get methods
     * 
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get patternUri
     * 
     * @return string
     */
    public function getPatternUri()
    {
        return $this->patternUri;
    }

    /**
     * Set action callback
     * 
     * @param string|\callable $action
     * @return Route
     * @throws \LogicException
     */
    public function setAction($action)
    {
        if (!is_string($action) && !is_callable($action)) {
            throw new \LogicException(sprintf('Route action given "%s" is invalid. String or Callable expected.', gettype($action)));
        }
        $this->action = $action;
        
        return $this;
    }

    /**
     * Set methods
     * 
     * @param string|array $methods
     * @return Route
     * @throws \LogicException
     */
    public function setMethods($methods)
    {
        if (is_string($methods)) {
            if (!isset(self::$validMethods[strtolower($methods)])) {
                throw new \LogicException(sprintf('Route method "%s" is invalid. Params expected "%s"', $methods, implode(', ', array_keys(self::$validMethods))));
            }
            $this->methods = array($methods);
        }
        
        if (is_array($methods)) {
            $this->methods = $methods;
        }
        
        return $this;
    }

    /**
     * Set name
     * 
     * @param string $name
     * @return Route
     * @throws \LogicException
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new \LogicException(sprintf('Route name given "%s" is invalid. String expected.', gettype($name)));
        }
        $this->name = $name;
        
        return $this;
    }

    /**
     * Set patternUri
     * 
     * @param string $patternUri
     * @return Route
     * @throws \LogicException
     */
    public function setPatternUri($patternUri)
    {
        if (!is_string($patternUri)) {
            throw new \LogicException(sprintf('Route pattern given "%s" is invalid. String expected.', gettype($patternUri)));
        }
        $this->patternUri = $patternUri;
        
        return $this;
    }
}
