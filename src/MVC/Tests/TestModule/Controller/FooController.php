<?php

namespace MVC\Tests\TestModule\Controller;

use MVC\Controller\Controller;
use MVC\MVC;
use MVC\Server\HttpRequest;

/**
 * Description of FooController
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class FooController extends Controller
{
    
    /**
     * Response indexAction for route /foo
     * 
     * @return string
     */
    public function indexAction()
    {
        return 'Foo Response indexAction';
    }
    
    /**
     * Redirect Action
     * 
     * @param MVC $mvc
     * @return string
     */
    public function redirectAction(MVC $mvc)
    {
        return $mvc->redirect('./redirected');
    }
    
    /**
     * Redirected Action
     * 
     * @return string
     */
    public function redirectedAction()
    {
        return 'Foo Response redirectedAction';
    }
    
    /**
     * With MVC Application Param Action
     * 
     * Calls by ::call(MVC $mvc, 'withMvcParamAction')
     * 
     * The access can be protected|private|public
     * 
     * @param MVC $mvc
     * @return string
     * @see MVC\Tests\ControllerTest
     */
    public function withMvcParamAction(MVC $mvc)
    {
        return sprintf('Foo Response with "%s" param action.', get_class($mvc));
    }
    
    /**
     * With MVC Application and HttpRequest Params Action
     * 
     * Calls by ::call(MVC $mvc, 'withMvcAndRequestParamsAction')
     * 
     * The access can be protected|private|public
     * 
     * @param MVC $mvc
     * @param HttpRequest $request
     * @return string
     * @see MVC\Tests\ControllerTest
     */
    public function withMvcAndRequestParamsAction(MVC $mvc, HttpRequest $request)
    {
        return sprintf('Foo Response with "%s" and "%s" params action.', get_class($mvc), get_class($request));
    }
    
    /**
     * With Route Param Action
     * 
     * Calls by ::call(MVC $mvc, 'withRouteParamsAction')
     * 
     * The access can be protected|private|public
     * 
     * @param string $foo_key
     * @return string
     * @see MVC\Tests\ControllerTest
     */
    public function withRouteParamsAction($foo_key)
    {
        return sprintf('Foo Response with Route "foo_key" => "%s" param action.', $foo_key);
    }
    
    /**
     * With View Vars Rendered Action
     * 
     * Calls by ::call(MVC $mvc, 'withViewVarsRenderedAction')
     * 
     * The access can be protected|private|public
     * 
     * @return array
     * @see MVC\Tests\ControllerTest
     */
    public function withViewVarsRenderedAction()
    {
        $arrayVars = array(
            'varString' => 'Value String',
            'varInt'    => 1,
            'varBool'   => true,
            'varArray'  => array('key' => 'value')
        );
        
        $arrayVars['controllerCode'] = <<<EOF
public function withViewVarsRenderedAction()
{
    \$arrayVars = array(
        'varString' => 'Value String',
        'varInt'    => 1,
        'varBool'   => true,
        'varArray'  => array('key' => 'value')
    );

    return \$arrayVars;
}
EOF;
        $arrayVars['templateCode']   = file_get_contents($this->view->templatesPath . '/Foo/with_view_vars_rendered.php');
                
        return $arrayVars;
    }
    
    /**
     * Without Params Action
     * 
     * Calls by ::call(MVC $mvc, 'withoutParamsAction')
     * 
     * The access can be protected|private|public
     * 
     * @return string
     * @see MVC\Tests\ControllerTest
     */
    public function withoutParamsAction()
    {
        return 'Foo Response without params.';
    }

}
