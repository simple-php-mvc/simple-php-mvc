<?php

namespace MVC\Tests;

/**
 * Description of ControllerReflectionTest
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class ControllerReflectionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider controllerClass
     */
    public function testController($controllerClass, $action)
    {
        $reflectionClass = new \ReflectionClass($controllerClass);
        $reflectionMethod = $reflectionClass->getMethod($action);
        $reflectionParams = $reflectionMethod->getParameters();
        
        print "Number of parameters: {$reflectionMethod->getNumberOfParameters()}\n";
        print "Number of required parameters: {$reflectionMethod->getNumberOfRequiredParameters()}\n";
        
        foreach($reflectionParams as $key => $param) {
            print "Parameter {$key}: {$param->getClass()->name} \${$param->name}\n";
        }
    }
    
    public function controllerClass()
    {
        return array(
            array(
                'MVC\\Tests\\TestModule\\Controller\\FooController',
                'indexAction'
            )
        );
    }

}
