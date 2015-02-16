<?php

namespace MVC\Tests;

use MVC\MVC;

/**
 * Application example
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class App extends MVC
{
    
    /**
     * {@inheritdoc}
     */
    public function __construct() {
        parent::__construct(array(
            'templates_path' => __DIR__ . '/TestModule/Resources/views'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setModules()
    {
        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setProviders()
    {
        return array();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setRoutes()
    {
        return array();
    }
}
