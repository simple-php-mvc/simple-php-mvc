<?php

namespace MVC\Controller;

use MVC\MVC;

/**
 * Controller Interface
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
interface ControllerInterface
{
    
    /**
     * Call class method
     * 
     * @param MVC $mvc         MVC Application object
     * @param string $method   Action Controller
     * @param string $fileView File name view
     * @return array           Response
     */
    public function call(MVC $mvc, $method, $fileView);
    
}
