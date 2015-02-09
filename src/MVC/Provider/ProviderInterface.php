<?php

/**
 * Provider Interface
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC
 */

namespace MVC\Provider;

use MVC\MVC;

interface ProviderInterface
{    
    /**
     * Bootstrap of the provider
     * 
     * @access public
     * @param MVC $mvc
     */
    public function boot(MVC $mvc);
    
    /**
     * Returns the provider name (the class short name).
     *
     * @return string The Provider name
     */
    public function getName();

    /**
     * Register the provider properties and functions
     * 
     * @access public
     * @param MVC $mvc
     */
    public function register(MVC $mvc);
}
