<?php
/**
 * Console Symfony Provider for run console commands
 * 
 * @author Ramon Serrano <ramon.calle.88@gmail.com>
 * @package MVC\Tests\Provider
 */

namespace MVC\Tests\Provider;

use MVC\MVC;
use MVC\Module\Module;
use MVC\Provider\Provider;
use Symfony\Component\Console\Application;

class ConsoleSymfonyProvider extends Provider
{

    /**
     * Default options
     *
     * @var array
     */
    static $defaultOptions = array(
        'modules'  => array(),
        'commands' => array()
    );

    /**
     * Registered modules
     *
     * @var Module[]
     */
    protected $modules = array();

    /**
     * Doctrine DBAL Provider Construct
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(self::$defaultOptions, $options);
        $this->modules = $options['modules'];
    }

    /**
     * Bootstrap of the provider
     *
     * @access public
     * @param MVC $mvc
     */
    public function boot(MVC $mvc)
    {
    }

    /**
     * Register the Console Symfony
     *
     * @access public
     * @param MVC $mvc
     */
    public function register(MVC $mvc)
    {
        $application = new Application('Simple PHP MVC', '1.5');

        if (!$mvc->hasCvpp('symfony.console')) {
            $mvc->setCvpp('symfony.console', $application);
        }

        foreach ($this->modules as $module) {
            $module->registerCommands($mvc->getCvpp('symfony.console'));
        }
    }
}