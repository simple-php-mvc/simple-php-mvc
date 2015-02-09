<?php

namespace MVC\Application;

use MVC\Module\Module;
use MVC\Provider\Provider;
use MVC\Server\HttpRequest;
use MVC\Server\Response;
use MVC\Server\Route;
use MVC\Server\Router;
use MVC\View;

/**
 * Description of Container
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class Container
{

    /**
     * Application Dir
     * 
     * @var string
     */
    protected $appDir;

    /**
     * Array Modules
     * 
     * @var Module[]
     */
    protected $modules;
    
    /**
     * Array Providers
     * 
     * @var Provider[]
     */
    protected $providers;
    
    /**
     * Object HttpRequest
     * 
     * @var HttpRequest 
     */
    protected $request;
    
    /**
     * Object Response
     * 
     * @var Response
     */
    protected $response;
    
    /**
     * Object Router
     * 
     * @var Router
     */
    protected $router;
    
    /**
     * Routes Collection
     * 
     * @var Route[]
     */
    protected $routes;
    
    /**
     * MVC Options
     * 
     * @var array
     */
    protected $settings = array(
        'debug' => true
    );
    
    /**
     * Object View
     * 
     * @var View
     */
    protected $view;
    
    /**
     * MVC Application Container Construct
     * 
     * @param array $settings
     */
    function __construct(array $settings)
    {
        $this->appDir = (isset($settings['appDir'])) ? $settings['appDir'] : null;
        $this->modules = array();
        $this->providers = array();
        $this->routes = array();
        $this->setRequest(new HttpRequest());
        $this->setResponse(new Response());
        $this->setRouter(new Router());        
        $this->setSettings($settings);
        $this->setView(new View());
    }
    
    /**
     * Add Module
     * 
     * @param Module $module
     * @return Container
     * @throws \LogicException
     */
    public function addModule(Module $module)
    {
        $name = $module->getName();
        if (isset($this->modules[$name])) {
            throw new \LogicException(sprintf('Trying to register two modules with the same name "%s"', $name));
        }
        $this->view->addTemplatePath($module->getPath() . '/Resources/views');
        $this->modules[$name] = $module;
        
        return $this;
    }
    
    /**
     * Add Module
     * 
     * @param Provider $provider
     * @return Container
     * @throws \LogicException
     */
    public function addProvider(Provider $provider)
    {
        $name = $provider->getName();
        if (isset($this->providers[$name])) {
            throw new \LogicException(sprintf('Trying to register two providers with the same name "%s"', $name));
        }
        $this->providers[$name] = $provider;
        
        return $this;
    }
    
    /**
     * Add Route
     * 
     * @param Route|array $route
     * @return Container
     * @throws \LogicException
     */
    public function addRoute($route)
    {
        if (!$route instanceof Route && !is_array($route)) {
            throw new \LogicException('Route invalid. Expected Route instance or array.');
        } elseif ($route instanceof Route) {
            $name = $route->getName();
            if (isset($this->routes[$name])) {
                throw new \LogicException(sprintf('Trying to register two routes with the same name "%s"', $name));
            }
            $this->routes[$route->getName()] = $route;
        } elseif (is_array($route) && !count($route) == 4) {
            throw new \LogicException('Array Route invalid. Expected array(string|array method, string pattern, callback|string action, string name).');
        } else {
            list($methods, $patternUri, $action, $name) = array_values($route);
            
            $this->routes[$name] = new Route($methods, $patternUri, $action, $name);
        }
        
        return $this;
    }
    
    /**
     * Get MVC Application Dir
     * 
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }
    
    /**
     * Get Modules
     * 
     * @return Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }
    
    /**
     * Set setting from name
     * 
     * @param string $name
     * @return mixed
     * @throws \LogicException
     */
    public function getSetting($name)
    {
        if (!isset($this->settings[$name])) {
            throw new \LogicException(sprintf('The setting "%s" don\'t exists.', $name));
        }
        return $this->settings[$name];
    }

    /**
     * Get settings
     * 
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Get Provider
     * 
     * @param string $name
     * @return Provider
     * @throws \LogicException
     */
    public function getProvider($name)
    {
        if (!isset($this->providers[$name])) {
            throw new \LogicException(sprintf('The provider "%s" don\'t exists.', $name));
        }
        return $this->providers[$name];
    }
    
    /**
     * Get Providers
     * 
     * @return Provider[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Get HttpRequest
     * 
     * @return HttpRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get Response
     * 
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get Router
     * 
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Get Route from name
     * 
     * @param string $name
     * @return Route
     * @throws \LogicException
     */
    public function getRoute($name)
    {
        if (!isset($this->routes[$name])) {
            throw new \LogicException(sprintf('Route "%s" not found.', $name));
        }
        
        return $this->routes[$name];
    }
    
    /**
     * Get Routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get View
     * 
     * @return View
     */
    public function getView()
    {
        return $this->view;
    }
    
    /**
     * Has Module name
     * 
     * @param string $name
     * @return boolean
     */
    public function hasModule($name)
    {
        return isset($this->modules[$name]);
    }

    /**
     * Has Provider name
     * 
     * @param string $name
     * @return boolean
     */
    public function hasProvider($name)
    {
        return isset($this->providers[$name]);
    }
    
    /**
     * Has Route name
     * 
     * @param string $name
     * @return boolean
     */
    public function hasRoute($name)
    {
        return isset($this->routes[$name]);
    }
    
    /**
     * Has Setting name
     * 
     * @param string $name
     * @return boolean
     */
    public function hasSetting($name)
    {
        return (isset($this->settings[$name]));
    }

    /**
     * Set MVC Application Dir
     * 
     * @param string $appDir
     * @return Container
     */
    public function setAppDir($appDir)
    {
        $this->appDir = $appDir;
        
        return $this;
    }

    /**
     * Set HttpRequest
     * 
     * @param HttpRequest $request
     * @return Container
     */
    public function setRequest(HttpRequest $request) 
    {
        $this->request = $request;
        
        return $this;
    }

    /**
     * Set Response
     * 
     * @param Response $response
     * @return Container
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        
        return $this;
    }

    /**
     * Set Router
     * 
     * @param Router $router
     * @return Container
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        
        return $this;
    }
    
    /**
     * Set settings
     * 
     * @param array $settings
     * @return Container
     */
    public function setSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        
        if (isset($settings['templates_path'])) {
            $this->view->templatesPath = $this->settings['templates_path'];
        }
        
        return $this;
    }

    /**
     * Set View
     * 
     * @param View $view
     * @return Container
     */
    public function setView(View $view)
    {
        $this->view = $view;
        
        return $this;
    }

}
