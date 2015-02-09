<?php

/**
 * PDO 
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC\DataBase
 */

namespace MVC\DataBase;

class PDO
{
    /**
     * PDO Object
     * @access protected
     * @var \PDO
     */
    protected $_pdo;
    
    /**
     * Executes number
     * @access public
     * @var int
     */
    public $numExecutes;
    
    /**
     * Statements number
     * @access public
     * @var int
     */
    public $numStatements;
    
    /**
     * Instance of PDO
     * @access public
     * @var PDO
     */
    public static $instance;
    
    /**
     * Construct of the class
     * @param string $dsn
     * @param string $user
     * @param string $passwd
     * @param mixed $driverOptions
     */
    function __construct($dsn, $user = null, $passwd = null, $driverOptions = null)
    {
        $this->_pdo = new \PDO($dsn, $user, $passwd, $driverOptions);
        $this->numExecutes = 0;
        $this->numStatements = 0;
    }
    
    /**
     * Call a class method
     * @access public
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array(array(&$this->_pdo, $method), $arguments);
    }
    
    /**
     * Get the instance of the class
     * @access public
     * @param string $dsn          URI of the driver
     * @param string $user         Connection user
     * @param string $passwd       Connection password
     * @param array $driverOptions Driver options
     * @return PDO
     */
    public static function __getInstance($dsn, $user = null, $passwd = null, array $driverOptions = array())
    {
        if (!self::$instance) {
            self::$instance = new self($dsn, $user, $passwd, $driverOptions);
        }
        return self::$instance;
    }

    /**
     * Prepare the statement SQL
     * @access public
     * @param string $sql          Statement SQL
     * @param srray $driverOptions Driver options
     * @return PDOStatement
     */
    public function prepare($sql, array $driverOptions = array())
    {
        $this->numStatements++;

        $pdos = $this->_pdo->prepare($sql, $driverOptions);
        
        return new PDOStatement($this, $pdos);
    }
    
    /**
     * Executes the statement query
     * @access public
     * @param string $sql Statement SQL
     * @return PDOStatement
     */
    public function query($sql)
    {
        $this->numExecutes++;
        $this->numStatements++;
        
        $pdos = $this->_pdo->query($sql);
        
        return new PDOStatement($this, $pdos);
    }
    
    /**
     * Executes directly the statement SQL
     * @access public
     * @param string $sql Statement SQL
     * @return int Filas afectadas
     */
    public function exec($sql)
    {
        $this->numExecutes++;
           
        return $this->_pdo->exec($sql);
    }

}
