<?php

namespace MVC\Provider;

/**
 * Description of Provider
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class Provider implements ProviderInterface
{
    
    /**
     * Provider name
     * 
     * @var string
     */
    protected $name;
    
    /**
     * Provider options
     * 
     * @var array
     */
    protected $options = array();
    
    /**
     * Provider construct
     * 
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }
    
    /**
     * Returns the provider name (the class short name).
     *
     * @return string The Provider name
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name : substr($name, $pos + 1);
    }
    
    /**
     * Get option value from name
     * 
     * @param string $name
     * @return mixed
     * @throws \LogicException
     */
    final public function getOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new \LogicException(sprintf('The option "%s" don\'t exists.', $name));
        }
        return $this->options[$name];
    }
    
    /**
     * Get options
     * 
     * @return array
     */
    final public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Set option 
     * 
     * @param string $name
     * @param mixed $value
     * @return Provider
     */
    final public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        
        return $this;
    }

}
