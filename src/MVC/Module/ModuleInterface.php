<?php

namespace MVC\Module;

use Symfony\Component\Console\Application;
use MVC\Injection\ExtensionInterface;

/**
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
interface ModuleInterface
{
    /**
     * Returns the container extension that should be implicitly loaded.
     *
     * @return ExtensionInterface|null The default extension or null if there is none
     */
    public function getModuleExtension();

    /**
     * Returns the module name that this bundle overrides.
     *
     * Despite its name, this method does not imply any parent/child relationship
     * between the modules, just a way to extend and override an existing
     * bundle.
     *
     * @return string The Module name it overrides or null if no parent
     */
    public function getParent();

    /**
     * Returns the module name (the class short name).
     *
     * @return string The Module name
     */
    public function getName();

    /**
     * Gets the Module namespace.
     *
     * @return string The Module namespace
     */
    public function getNamespace();

    /**
     * Gets the Module directory path.
     *
     * The path should always be returned as a Unix path (with /).
     *
     * @return string The Module absolute path
     */
    public function getPath();
    
    /**
     * Register Modules comands
     */
    public function registerCommands(Application $application);
    
}
