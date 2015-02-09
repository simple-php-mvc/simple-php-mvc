<?php

namespace MVC\DataBase;

use MVC\MVC;
use MVC\DataBase\PDO;
use MVC\Provider\Provider;

/**
 * Description of PdoProvider
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class PdoProvider extends Provider
{
    
    /**
     * Default options
     * 
     * @var array
     */
    static $defaultOptions = array(
        'charset'       => 'utf8',
        'dbname'        => 'simple_php_mvc',
        'driverOptions' => null,
        'host'          => 'localhost',
        'user'          => 'root',
        'passwd'        => '',
        'port'          => null,
        'servicedb'     => 'mysql',
        'unix_socket'   => null
    );
    
    /**
     * PDO Instance
     * 
     * @var PDO
     */
    protected $pdo;

    /**
     * PDO Provider construct
     * 
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->options = array_merge(self::$defaultOptions, $options);
        
        $dsn = $this->options['servicedb'] . ':';
        if (isset($this->options['host']) && ($this->options['host'] != '' && !is_null($this->options['host']))) {
            $dsn .= 'host=' . $this->options['host'] . ';';
        }
        if (isset($this->options['port']) && !is_null($this->options['port'])) {
            $dsn .= 'port=' . $this->options['port'] . ';';
        }
        if (isset($this->options['dbname']) && !is_null($this->options['dbname'])) {
            $dsn .= 'dbname=' . $this->options['dbname'] . ';';
        }
        if (isset($this->options['unix_socket']) && !is_null($this->options['unix_socket'])) {
            $dsn .= 'unix_socket=' . $this->options['unix_socket'] . ';';
        }
        if (isset($this->options['charset']) && !is_null($this->options['charset'])) {
            $dsn .= 'charset=' . $this->options['charset'] . ';';
        }
        
        $this->options['dsn'] = $dsn;
    }

    /**
     * {@inheritdoc}
     */
    public function boot(MVC $mvc)
    {
        
    }
    
    /**
     * Get pdo
     * 
     * @return PDO
     */
    public function getPdo() 
    {
        return $this->pdo;
    }

    /**
     * {@inheritdoc}
     */
    public function register(MVC $mvc)
    {
        $this->pdo = new PDO($this->options['dsn'], $this->options['user'], $this->options['passwd'], $this->options['driverOptions']);
        
        if (!$mvc->hasCvpp('pdo')) {
            $mvc->setCvpp('pdo', $this->pdo);
        }
    }

}
