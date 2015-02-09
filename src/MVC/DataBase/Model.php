<?php

/**
 * Modelo abstracto del que requeriran los demas modelos
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC\DataBase
 */

namespace MVC\DataBase;

abstract class Model
{

    /**
     * Table name of model
     * @access protected
     * @var string
     */
    protected $_table;
    
    /**
     * PDO Object
     * @access protected
     * @var PDO
     */
    protected $_pdo;
    
    /**
     * Instance of model
     * @access public
     * @var Model
     */
    public static $instance;
    
    /**
     * Construct of the class
     * @access public
     * @param PDO $pdo
     * @param string $table
     * @return void
     */
    public function __construct(PDO $pdo, $table)
    {
        $this->_pdo = $pdo;
        $this->_table = $table;
    }

    /**
     * Get the instance
     * @access public
     * @param PDO $pdo
     * @param string $table
     * @return Model
     */
    public static function getInstance(PDO $pdo, $table)
    {
        if (!self::$instance) {
            self::$instance = new self($pdo, $table);
        }
        return self::$instance;
    }
    
    /**
     * Prepare de SQL query
     * @access protected
     * @param string $sql
     * @return PDOStatement
     */
    protected function query($sql)
    {
        return $this->_pdo->prepare($sql);
    }
    
    /**
     * Function SQL DELETE
     * @access protected
     * @param array $criteria
     * @return int 
     */
    protected function delete(array $criteria = array())
    {
        $conditions = array();
        
        foreach ($criteria as $field => $value) {
            $conditions[] = "$field = ?";
        }
        
        $sql = "DELETE FROM $this->_table WHERE " . implode(' AND ', $conditions);
        
        $pdos = $this->_pdo->prepare($sql);
        
        $pdos->execute(array_values($criteria));
        
        return $pdos->statement()->rowCount();
    }

    /**
     * Function SQL INSERT
     * @access protected
     * @param array $data
     * @return int
     */
    protected function insert(array $data = array())
    {
        $fields = array();
        $values = array();
        
        foreach ($data as $field => $value) {
            $fields[] = $field;
            $values[] = '?';
        }
        
        $sql = "INSERT INTO $this->_table " 
                . "(" . implode(', ', $fields) . ")"
                . "VALUES (" . implode(', ', $values) . ")";
                
        $pdos = $this->_pdo->prepare($sql);
        
        $pdos->execute(array_values($data));
        
        return $pdos->statement()->rowCount();
    }
    
    /**
     * Function SQL SELECT
     * @access protected
     * @param array $fields
     * @param array $criteria
     * @param array $operators
     * @param array $conditions
     * @return array
     * @throws \Exception
     */
    protected function select(array $fields, array $criteria = array(), array $operators = array(), array $conditions = array())
    {
        $sql = "";
        $stringConditions = array();
        
        if ((!empty($criteria) && !empty($operators)) && !empty($conditions)) {
            if ((count($criteria) == count($operators) && (count($criteria) - 1) == count($conditions)) 
                && ((count($operators) -1) != count($conditions) && count($criteria) > 1)) {
                throw new \Exception("The number of Criteria, Operators and Conditions are not equals.");
            }
            
            $criteriaFields = array_keys($criteria);
            
            $stringConditions[] = "$criteriaFields[0] $operators[0] ?";
            
            for ($i = 1; $i < count($criteriaFields); $i++) {
                $j = $i - 1;
                $stringConditions[] = "$conditions[$j] $criteriaFields[$i] $operators[$i] ?";
            }
            
            $sql = "SELECT " . implode(', ', $fields) . " FROM $this->_table " 
                . "WHERE " . implode(' ', $stringConditions);
        } elseif ((!empty($criteria) && !empty($operators)) && count($criteria) > 1) {
            if (count($criteria) != count($operators)) {
                throw new \Exception("The number of Criteria and Operators are not equals.");
            }
            
            $criteriaFields = array_keys($criteria);
            
            for ($i = 0; $i < count($criteriaFields); $i++) {
                $stringConditions[] = "$criteriaFields[$i] $operators[$i] ?";
            }
            
            $sql = "SELECT " . implode(', ', $fields) . " FROM $this->_table " 
                . "WHERE " . implode(' AND ', $stringConditions);
        } elseif (!empty($criteria)) {
            foreach ($criteria as $field => $value) {
                $stringConditions[] = "$field = ?";
            }
            
            $sql = "SELECT " . implode(', ', $fields) . " FROM $this->_table " 
                . "WHERE " . implode(' AND ', $stringConditions);
        } elseif (!empty($fields)) {
            $sql = "SELECT " . implode(', ', $fields) . " FROM $this->_table";
        }
        
        $pdos = $this->_pdo->prepare($sql);
        $pdos->execute(array_values($criteria));
        
        return $pdos->fetchAll();
    }
    
    /**
     * Function SQL UPDATE
     * @access protected
     * @param array $data
     * @param array $criteria
     * @return int 
     */
    protected function update(array $data = array(), array $criteria)
    {
        $set = array();
        foreach ($data as $columnName => $value) {
            $set[] = "$columnName = ?";
        }
        
        $params = array_merge(array_values($data), array_values($criteria));
        
        $sql = "UPDATE $this->_table SET " . implode(', ', $set)
                . " WHERE " . implode(' = ? AND ', array_keys($criteria)) . " = ?";
        
        $pdos = $this->_pdo->prepare($sql);
        
        $pdos->execute($params);
        
        return $pdos->statement()->rowCount();
    }
    
    /**
     * Find all
     * @access public
     * @return array
     */
    public function findAll()
    {
        return $this->select(array('*'));
    }
    
    /**
     * Find by column o field of the table
     * @access public
     * @param string $column
     * @param string $value
     * @return array
     */
    public function findBy($column, $value)
    {
        $criteria = array($column => $value);
        
        return $this->select(array('*'), $criteria);
    }

}
