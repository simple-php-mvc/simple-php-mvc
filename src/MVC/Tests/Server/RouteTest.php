<?php

namespace MVC\Tests\Server;

use MVC\Server\Route;

/**
 * Description of RouteTest
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleRoute()
    {
        $route = new Route('get', '/', function() {});
    }

}
