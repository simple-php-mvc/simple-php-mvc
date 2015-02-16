<?php

namespace MVC\Tests\Provider;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\Configuration,
    Doctrine\DBAL\DriverManager,
    MVC\MVC,
    MVC\Provider\Provider;

/**
 * Doctrine DBAL Provider
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class DoctrineDBALProvider extends Provider
{
    
    /**
     * Doctrine DBAL Connection
     * 
     * @var Connection
     */
    protected $connection;
    
    /**
     * Default options
     * 
     * @var array
     */
    static $defaultOptions = array(
        'charset'  => null,
        'driver'   => 'pdo_mysql',
        'dbname'   => null,
        'host'     => 'localhost',
        'user'     => 'root',
        'password' => null,
        'port'     => null,
    );

    /**
     * Doctrine DBAL Provider Construct
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(self::$defaultOptions, $options);
        
    }

    /**
     * Bootstrap of the Provider
     * @access public
     * @param MVC $mvc
     * @return void
     */
    public function boot(MVC $mvc) {}

    /**
     * Register the properties of the Doctrine DBAL Provider
     * @access public
     * @param MVC $mvc
     * @return void
     */
    public function register(MVC $mvc)
    {
        if (!$mvc->hasCvpp('dbal')) {
            $mvc->setCvpp('dbal', $this->connection);
        }
        $config = new Configuration();
        $this->connection = DriverManager::getConnection($this->options, $config);
    }

}
