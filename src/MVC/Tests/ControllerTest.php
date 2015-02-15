<?php

namespace MVC\Tests;

use MVC\Tests\TestModule\Controller\FooController;
use MVC\MVC;
use MVC\View;

/**
 * Description of CotrollerTest
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var FooController
     */
    protected $fooCtrl;
    
    /**
     * @var MVC
     */
    protected $mvcApp;
    
    protected function setUp()
    {
        $this->fooCtrl = new FooController(new View());
        $this->mvcApp = new App();
    }
    
    /**
     * With MVC Application Param Action
     */
    public function testWithMcvParamAction()
    {
        $response = $this->fooCtrl->call($this->mvcApp, 'withMvcParamAction');
        
        $this->assertTrue((is_array($response) && !empty($response)));
    }

    /**
     * With MVC Application and HttpRequest Params Action
     */
    public function testWithMcvAndRequestParamsAction()
    {
        $response = $this->fooCtrl->call($this->mvcApp, 'withMvcAndRequestParamsAction');
        
        $this->assertTrue((is_array($response) && !empty($response)));
    }
    
    /**
     * With MVC Application, HttpRequest and Route Params Action
     */
    public function testWithRouteParamsAction()
    {
        $this->mvcApp->request()->params = array('foo_key' => 'foo_value');
        
        $response = $this->fooCtrl->call($this->mvcApp, 'withRouteParamsAction');
        
        $this->assertTrue((is_array($response) && !empty($response)));
    }
    
    /**
     * With View Vars Rendered Action
     */
    public function testWithViewVarsRenderedAction()
    {
        $response = $this->fooCtrl->call($this->mvcApp, 'withViewVarsRenderedAction', 'with_view_vars_rendered.php');
        
        $this->assertTrue((is_array($response) && !empty($response)));
    }
    
    /**
     * Test Without Param Action
     */
    public function testWithoutParamsAction()
    {
        $response = $this->fooCtrl->call($this->mvcApp, 'withoutParamsAction');
        
        $this->assertTrue((is_array($response) && !empty($response)));
    }
    
}
