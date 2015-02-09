<?php

namespace MVC\Tests\Provider;

use MVC\MVC,
    MVC\Provider\Provider;

/**
 * Twig Framework Provider
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class TwigProvider extends Provider
{
    /**
     * Bootstrap of the Provider
     * @access public
     * @param MVC $app
     * @return void
     */
    public function boot(MVC $app) { }

    /**
     * Register the properties of the Twig Framework Provider
     * @access public
     * @param MVC $mvc
     * @return void
     */
    public function register(MVC $mvc)
    {
        $defaultOptions = array(
            'charset'          => $mvc->getSetting('charset'),
            'debug'            => $mvc->getSetting('debug'),
            'strict_variables' => $mvc->getSetting('debug'),
            'templates_path'   => array($mvc->getSetting('templates_path'))
        );
        
        $options = array_merge($defaultOptions, $this->options);
        
        $mvc->setCvpp('twig.loader.filesystem', new \Twig_Loader_Filesystem($options['path']));
        $mvc->setCvpp('twig.loader.array', new \Twig_Loader_Array($options['templates_path']));
        
        $mvc->setCvpp('twig.loader', new \Twig_Loader_Chain(array(
            $mvc->getCvpp('twig.loader.array'),
            $mvc->getCvpp('twig.loader.filesystem')
        )));
        
        $twig = new \Twig_Environment($mvc->getCvpp('twig.loader'), $options);
        $twig->addGlobal('mvc', $mvc);
        
        if ($options['debug']) {
            $twig->addExtension(new \Twig_Extension_Debug());
        }
        
        $mvc->setCvpp('twig', $twig);
    }

}
