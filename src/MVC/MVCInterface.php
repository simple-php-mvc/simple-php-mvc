<?php

namespace MVC;

/**
 * MVCInteface
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
interface MVCInterface
{
    /**
     * Get Application Dir 
     * 
     * @return string Application Dir
     */
    public function getAppDir();
    
    /**
     * Get registered modules
     * 
     * @return array Registered modules
     */
    public function getModules();
    
    /**
     * Get registered providers
     * 
     * @return array Registered providers
     */
    public function getProviders();
    
    /**
     * Set Modules to register
     * 
     * @return array
     */
    public function setModules();
    
    /**
     * Set Providers to register
     * 
     * @return array
     */
    public function setProviders();
    
    /**
     * Set Routes to register
     * 
     * @return array
     */
    public function setRoutes();
    
}
