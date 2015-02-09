<?php

namespace MVC\Tests\Provider;

use Monolog\Logger,
    Monolog\Handler\StreamHandler,
    MVC\MVC,
    MVC\Provider\Provider;

/**
 * Monolog Provider
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class MonologProvider extends Provider
{
    
    /**
     * Bootstrap of the Provider
     * @access public
     * @param MVC $mvc
     * @return void
     */
    public function boot(MVC $mvc) {}

    /**
     * Register the properties of the Monolog Provider
     * @access public
     * @param MVC $mvc
     * @return void
     */
    public function register(MVC $mvc)
    {
        $defaultOptions = array(
            'log_file' => $mvc->getAppDir() . '/logs/app_name.log',
            'log_name' => 'app_name'
        );
        
        $options = array_merge($defaultOptions, $this->options);
        
        $logger = new Logger($options['log_name']);
        $logger->pushHandler(new StreamHandler($options['log_file']));
        
        if (!$mvc->hasCvpp('monolog')) {
            $mvc->setCvpp('monolog', $logger);
        }
    }

}
