<?php

namespace MVC\Module;

use MVC\File\Explorer;
use Symfony\Component\Console\Application;
use MVC\Injection\Container;

/**
 * Description of Module
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
abstract class Module implements ModuleInterface
{
    /**
     * Module name
     * 
     * @var string
     */
    protected $name;
    protected $extension;
    protected $path;
    
    /**
     * Returns the module's extension.
     *
     * @return ExtensionInterface|null The container extension
     *
     * @throws \LogicException
     *
     */
    public function getModuleExtension()
    {
        if (null === $this->extension) {
            $class = $this->getModuleExtensionClass();
            if (class_exists($class)) {
                $extension = new $class();

                // check naming convention
                $basename = preg_replace('/Module$/', '', $this->getName());
                $expectedAlias = Container::underscore($basename);
                if ($expectedAlias != $extension->getAlias()) {
                    throw new \LogicException(sprintf(
                        'Users will expect the alias of the default extension of a bundle to be the underscored version of the bundle name ("%s"). You can override "Bundle::getContainerExtension()" if you want to use "%s" or another alias.',
                        $expectedAlias, $extension->getAlias()
                    ));
                }

                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }
    
    /**
     * Gets the Module namespace.
     *
     * @return string The Module namespace
     */
    public function getNamespace()
    {
        $class = get_class($this);

        return substr($class, 0, strrpos($class, '\\'));
    }
    
    /**
     * Returns the module name (the class short name).
     *
     * @return string The Module name
     */
    final public function getName()
    {
        if (null !== $this->name) {
            return $this->name;
        }

        $name = get_class($this);
        $pos = strrpos($name, '\\');

        return $this->name = false === $pos ? $name : substr($name, $pos + 1);
    }
    
    /**
     * Gets the Module directory path.
     *
     * @return string The Module absolute path
     */
    public function getPath()
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            $this->path = dirname($reflected->getFileName());
        }

        return $this->path;
    }
    
    /**
     * Get the Module Extension Class Injection
     * 
     * @return string Module Extension Class Injection
     */
    public function getModuleExtensionClass()
    {
        $basename = preg_replace('/Module$/', '', $this->getName());

        return $this->getNamespace() . '\\Injection\\'.$basename.'Extension';
    }
    
    /**
     * Returns the module name that this bundle overrides.
     *
     * Despite its name, this method does not imply any parent/child relationship
     * between the modules, just a way to extend and override an existing
     * bundle.
     *
     * @return string The Module name it overrides or null if no parent
     */
    public function getParent()
    {
    }
    
    /**
     * Register commands
     * 
     * @param Application $application
     * @return void
     */
    public function registerCommands(Application $application)
    {
        if (!is_dir($dir = $this->getPath() . '/Command')) {
            return;
        }
        $explorer = new Explorer($dir);
        $explorer->setSearchPattern('/[a-zA-Z0-9]Command\.php$/i');
        
        $prefixNS = $this->getNamespace() . '\\Command';
        foreach ($explorer->getFiles() as $file) {
            $namespace = $prefixNS;
            $class = $namespace . '\\' . $file->getBasename('.php');
            $r = new \ReflectionClass($class);
            if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                $application->add($r->newInstance());
            }
        }
    }
}
