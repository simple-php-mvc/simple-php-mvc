<?php

/**
 * PDOStatement extendido
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC\DataBase
 */

namespace MVC\DataBase;

class PDOStatement
{

    /**
     * Object PDO
     * @access protected
     * @var PDO
     */
    protected $_pdo;
    
    /**
     * Object PDOStatement
     * @access protected
     * @var \PDOStatement
     */
    protected $_statement;
    
    /**
     * Construct of the class
     * @access protected
     * @param PDO $pdo
     * @param \PDOStatement $pdos
     * @return void
     */
    function __construct(PDO $pdo, \PDOStatement $statement)
    {
        $this->_pdo = $pdo;
        $this->_statement = $statement;
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
        return call_user_func_array(array(&$this->_statement, $method), $arguments);
    }
    
    /**
     * Bind a value of the column or field table
     * @access public
     * @param string $column
     * @param mixed $param
     * @param string $type
     * @return void 
     */
    public function bindColumn($column, &$param, $type = null)
    {
        if ($type === null) :
            $this->_statement->bindColumn($column, $param);
        else :
            $this->_statement->bindColumn($column, $param, $type);
        endif;
    }
    
    /**
     * Bind a value of the param SQL
     * @access public
     * @param string $column
     * @param mixed $param
     * @param string $type
     * @return void
     */
    public function bindParam($column, &$param, $type = null)
    {
        if ($type === null) :
            $this->_statement->bindParam($column, $param);
        else :
            $this->_statement->bindParam($column, $param, $type);
        endif;
    }
    
    /**
     * Execute the current statement
     * @access public
     * @param array $params Params of the Statement SQL
     * @return int
     */
    public function execute(array $params = array())
    {
        $this->_pdo->numExecutes++;
        return $this->_statement->execute($params);
    }
    
    /**
     * Fetch the current result
     * @access public
     * @return \stdClass
     */
    public function fetch()
    {
        return $this->_statement->fetch(\PDO::FETCH_CLASS);
    }
    
    /**
     * Fetch all results of the current statement
     * @access public
     * @return array
     */
    public function fetchAll()
    {
        return $this->_statement->fetchAll(\PDO::FETCH_CLASS);
    }

    /**
     * Properties PDOStatement Getter
     * @access public
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_statement->$property;
    }

    /**
     * Returns the current PDOStatement
     * @access public
     * @return \PDOStatement
     */
    public function statement()
    {
        return $this->_statement;
    }

}
