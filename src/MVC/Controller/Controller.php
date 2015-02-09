<?php

/**
 * Controller
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC
 */

namespace MVC\Controller;

use MVC\MVC,
    MVC\View;

abstract class Controller implements ControllerInterface
{

    /**
     * Response of Controller
     * @access protected
     * @var array
     */
    protected $response = array();

    /**
     * Object of the View
     * @access protected
     * @var View
     */
    protected $view;

    /**
     * Create the object View
     * 
     * @param View $view
     */
    public function __construct(View $view = null)
    {
        if (null === $view) {
            $this->view = new View();
        } else {
            $this->view = $view;
        }
    }

    /**
     * Call a action of controller
     * 
     * @access public
     * @param MVC $mvc         MVC Application object
     * @param string $method   Method or Function of the Class Controller
     * @param string $fileView String of the view file
     * @return array           Response array
     * @throws \LogicException
     */
    final public function call(MVC $mvc, $method, $fileView = null)
    {
        if (!method_exists($this, $method)) {
            throw new \LogicException(sprintf('Method "s" don\'t exists.', $method));
        }
        # Replace the view object
        $this->view = $mvc->view();
        # Arguments of method
        $arguments = array();
        # Create a reflection method
        $reflectionMethod = new \ReflectionMethod(get_class($this), $method);
        $reflectionParams = $reflectionMethod->getParameters();
        
        foreach ($reflectionParams as $param) {
            if ($paramClass = $param->getClass()) {
                $className = $paramClass->name;
                if ($className === 'MVC\\MVC' || $className === '\\MVC\\MVC') {
                    $arguments[] = $mvc;
                } elseif ($className === 'MVC\\Server\\HttpRequest' || $className === '\\MVC\\Server\\HttpRequest') {
                    $arguments[] = $mvc->request();
                }
            } else {
                foreach ($mvc->request()->params as $keyReqParam => $valueReqParam) {
                    if ($param->name === $keyReqParam) {
                        $arguments[] = $valueReqParam;
                        break;
                    }
                }
            }
        }
        
        $response = call_user_func_array($reflectionMethod->getClosure($this), $arguments);

        if (empty($response)) {
            throw new \LogicException('Response null returned.');
        }
        
        if (is_string($response)) {
            $this->response['body'] = $response;
        } elseif ($mvc->request()->isAjax()) {
            $this->response['body'] = $this->renderJson($response);
        } elseif(is_array($response)) {
            if (!$fileView) {
                throw new \LogicException('File view is null.');
            }
            $class = explode("\\", get_called_class());
            $classname = end($class);
            // Class without Controller
            $classname = str_replace('Controller', '', $classname);
            $file = $classname . "/{$fileView}";
            $this->response['body'] = $this->renderHtml($file, $response);
        }

        return $this->response;
    }
    
    /**
     * Get the View object
     * @access public
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }
    
    /**
     * Converts the supplied value to JSON.
     * @access public
     * @param mixed $value    The value to encode.
     * @return string
     */
    public function renderJson($value)
    {
        $options = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;
        return json_encode($value, $options);
    }

    /**
     * Renders a view.
     * @access public
     * @param string $file      The file to be rendered.
     * @param array $vars       The variables to be substituted in the view.
     * @return mixed
     */
    public function renderHtml($file, $vars = array())
    {
        return $this->view->render($file, $vars);
    }
    
    /**
     * Set View
     * 
     * @param View $view
     * @return Controller
     */
    public function setView(View $view)
    {
        $this->view = $view;
        
        return $this;
    }

}
