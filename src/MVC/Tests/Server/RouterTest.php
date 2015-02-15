<?php

namespace MVC\Tests\Server;

use MVC\Server\HttpRequest;
use MVC\Server\Route;
use MVC\Server\Router;

/**
 * Description of RouterTest
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    public function provider()
    {
        return array(
            array(
                'request' => HttpRequest::getInstance()->setUrl('/foo'),
                'routes' => array(
                    new Route(["GET", "POST"], '/foo', 'MVC\\Tests\\EjemploModule\\Controller\\FooController::fooAction', 'foo'),
                )
            )
        );
    }
    
    /**
     * @dataProvider provider
     */
    public function testRouter(HttpRequest $request, array $routes)
    {
        $router = new Router();
        $parsed = $router->parse($request->setMethod('GET'), $routes);
        
        $this->assertTrue(is_callable($parsed['action']));
        $this->assertTrue(is_array($parsed));
        $this->assertTrue($parsed['params'] instanceof \stdClass);
        $this->assertTrue(is_object($parsed['params']));
    }
    
}
