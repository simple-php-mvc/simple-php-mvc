<?php


namespace MVC\DataBase;

use Doctrine\DBAL\Connection,
    Doctrine\DBAL\DBALException,
    Doctrine\DBAL\Driver\Statement,
    Doctrine\DBAL\Query\QueryBuilder,
    \Exception;

/**
 * Description of DBALModel
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class DBALModel
{
    
    /**
     * @var int $_affectedRows Filas afectadas en el sql
     */
    protected $_affectedRows = 0;
    
    /**
     * @var string $_alias Alias de la tabla del modelo 
     */
    protected $_alias;
    
    /**
     * @var Connection $_db Objeto de la conección a base de datos de Silex
     */
    protected $_db;
    
    /**
     * @var array $_errors Arreglo de errores comunes de Doctrine DBAL
     */
    protected $_errors = array(
        'HY093' => 'Parámetros no dados.',
        '42S02' => 'Tabla o vista no encontrada.',
        '42000' => 'Error de sintaxis.',
        '23000' => 'Ambigüedad.'
    );
    
    /**
     * @var QueryBuilder $_queryBuilder
     */
    protected $_queryBuilder;
    
    /**
     * @var string $_sql SQL que se ejecuta al final de armarlo
     */
    protected $_sql = "";
    
    /**
     * @var Statement $_stmt
     */
    protected $_stmt;
    
    /**
     * @var string $_table Nombre de la tabla en la base de datos
     */
    protected $_table;
    
    /**
     * @var int $id Last insert id de la tabla o id del registro
     */
    public $id;
    
    /**
     * @param Connection $db Objeto de la conección de doctrine con la base de datos
     * @param string $table  Nombre de la tabla en la base de datos
     */
    public function __construct(Connection $db, $table = "")
    {
        if (!is_null($db)) {
            $this->_db = $db;
            $this->_queryBuilder = $this->_db->createQueryBuilder();
        }
        if (!is_null($table)) {
            $this->_table = $table;
        }
    }
    
    /**
     * @return Connection Retorna el objeto de la conección a base de datos de Silex
     */
    public function db()
    {
        return $this->_db;
    }
    
    /**
     * @return QueryBuilder
     */
    public function queryBuilder()
    {
        return $this->_queryBuilder;
    }
    
    /**
     * @param array $criteria Criterios de la consulta DELETE adoptados en el WHERE
     *
     * @return int Filas afectadas
     */
    protected function _delete(array $criteria = array())
    {
        if (!is_null($this->_table)) {
            $this->_affectedRows = $this->_db->delete($this->_table, $criteria);
            return $this->_affectedRows;
        }
    }
    
    /**
     * @param array $data Arreglo asociativo de los campos a insertar del registro
     *
     * @return int Filas afectadas
     */
    protected function _insert(array $data = array())
    {
        if (!is_null($this->_table)) {
            $this->_affectedRows = $this->_db->insert($this->_table, $data);
            if ($this->_affectedRows > 0) {
                $this->id = $this->_db->lastInsertId();
            }
            return $this->_affectedRows;
        }
    }
    
    /**
     * @param mixed $table        Tabla(s) para el sentencia INNER JOIN
     * @param mixed $field_first  Primer(os) campo(s). Primer campo en la condición del INNER JOIN
     * @param mixed $field_second Segundo(os) campo(s). Primer campo en la condición del INNER JOIN
     * @param mixed $operator     Operador(es) para el INNER JOIN.
     *
     * @return string             Sentencia SQL INNER JOIN.
     * @throws Exception
     */
    protected function _join($table, $field_first, $field_second, $operator)
    {
        $join = "";
        #If the parameters are array
        if ((is_array($table) && is_array($field_first)) && (is_array($field_second) && is_array($operator))) {
            #If the number of keys of the parameters are iquals
            if ((count($table) == count($field_first) && count($table) == count($field_second)) && count($table) == count($operator)) {
                for ($i = 0; $i < count($table); $i++) {
                    $join .= " INNER JOIN $table[$i] ON $field_first[$i] $operator[$i] $field_second[$i] ";
                }
            } else {
                throw new Exception("Los parámetros no tienen el mismo número de elementos.");
            }
        } elseif (!is_array($table) || !is_array($field_first) || !is_array($field_second) || !is_array($operator)) {
            $join = " INNER JOIN $table ON $field_first $operator $field_second ";
        } else {
            throw new Exception("Los parámetros no son del mismo tipo");
        }
        return $join;
    }
    
    /**
     * @param string $sql     Consulta SQL a la base de datos
     * @param array $criteria Criterios de la consulta SQL adoptados en el WHERE
     *
     * @return Statement Retorna el objeto de Doctrine Statement
     * @throws DBALException Error de Doctrine
     * @throws Exception Error en el SQL
     */
    protected function _query($sql = "", array $criteria = array())
    {
        try {
            // Preparar el SQL
            $this->_stmt = $this->_db->prepare($sql);
            
            // Agregar los parametros
            foreach ($criteria as $param => $value) {
                if (is_integer($param)) {
                    $this->_stmt->bindValue(($param + 1), $value);
                }
                if (is_string($param)) {
                    $this->_stmt->bindParam($param, $value);
                }
            }
            // Ejecutar el SQL
            $this->_stmt->execute();
        } catch (DBALException $dbalException) {
            #throw $dbalException;
        }
        if (in_array($this->_stmt->errorCode(), array_keys($this->_errors))) {
            throw new Exception($this->_errors[$this->_stmt->errorCode()] . " SQL: $sql");
        }
        return $this->_stmt;
    }
    
    /**
     * @param string $sql     Sentencia SQL SELECT básica
     * @param array $join     Arreglos de los parámetros del INNER JOIN
     * @param string $where   Sentencia SQL WHERE que identifica la condición de la consulta
     * @param array $criteria Criterios de la consulta SELECT adoptados en el WHERE
     *
     * @return array $rows Arreglo asociativo de los registros
     * @throws Exception   Lanza una exception si los valores del INNER JOIN son incorrectos
     */
    protected function _select($sql = "", array $join = array(), $where = "", array $criteria = array())
    {
        if ($this->_sql != $sql)
            $this->_sql = $sql;
        if (is_array($join)) {
            foreach ($join as $currentJoin) {
                if (is_array($currentJoin) && count($currentJoin) == 4) {
                    $keys = array_keys($currentJoin);
                    $this->_sql .= $this->_join($currentJoin[$keys[0]], $currentJoin[$keys[1]], $currentJoin[$keys[2]], $currentJoin[$keys[3]]);
                } elseif (is_string($currentJoin) && count($join) == 4) {
                    $keys = array_keys($join);
                    $this->_sql .= $this->_join($join[$keys[0]], $join[$keys[1]], $join[$keys[2]], $join[$keys[3]]);
                    break;
                }
            }
        } elseif (empty($join)) {
            throw new Exception("Valores incorrectos del join. Array['table'], Array['field_first'], Array['field_second'], Array['operator']. Array[0], Array[1], Array[2], Array[3].");
        }
        $stmt = $this->_query("$this->_sql $where", $criteria);
        $rows = $stmt->fetchAll();
        return $rows;
    }
    
    /**
     * @param array $fields   Arreglo de los campos del SELECT a consultar
     * @param array $join     Arreglos de los parámetros del INNER JOIN
     * @param string $where   Sentencia SQL WHERE que identifica la condición de la consulta
     * @param array $criteria Criterios de la consulta SELECT adoptados en el WHERE
     *
     * @return array  Arreglo asociativo de los registros
     * @throws Exception   Lanza una exception si los valores del INNER JOIN son incorrectos
     */
    protected function _selectFields(array $fields = array(), array $join = array(), $where = "", array $criteria = array())
    {
        if (empty($fields)) {
            $fields = array('*');
        }
        $this->_sql = "SELECT " . implode(',', $fields) . " FROM $this->_table";
        return $this->_select($this->_sql, $join, $where, $criteria);
    }
    
    /**
     * @param array $data     Arreglo asociativo de los campos a actualizar del registro
     * @param array $criteria Criterios de la consulta UPDATE adoptados en el WHERE
     *
     * @return int Filas afectadas
     */
    protected function _update(array $data = array(), array $criteria = array())
    {
        if (!is_null($this->_table)) {
            $this->_affectedRows = $this->_db->update($this->_table, $data, $criteria);
            return $this->_affectedRows;
        }
    }
    
    /**
     * @param array $fields   Arreglo de los campos del SELECT a consultar
     * @param array $join     Arreglos de los parámetros del INNER JOIN
     * @param string $where   Sentencia SQL WHERE que identifica la condición de la consulta
     * @param array $criteria Criterios de la consulta SELECT adoptados en el WHERE
     *
     * @return array
     */
    public function getAll(array $fields = array(), array $join = array(), $where = "", array $criteria = array())
    {
        if (!is_null($this->_table)) {
            if (empty($fields)) {
                $this->_sql = "SELECT * FROM $this->_table";
                return $this->_select($this->_sql, $join, $where, $criteria);
            }
            else {
                return $this->_selectFields($fields, $join, $where, $criteria);
            }
        }
    }
    
    /**
     * @param int $id Id del registro
     *
     * @return array  Arreglo asociativo del registro
     */
    public function getById($id)
    {       
        if (!is_null($this->_table) && !is_null($id)) {
            $this->_sql = "SELECT * FROM $this->_table WHERE id = $id";
            $row = $this->_select($this->_sql);
            return (isset($row[0])) ? $row[0] : null;
        }
    }
    
    /**
     * @param string $alias Alias de la tabla
     */
    public function setAlias($alias)
    {
        $this->_alias = $alias;
    }
    
    /**
     * @param string $table Nombre de la tabla en la base de datos
     */
    public function setTable($table)
    {
        $this->_table = $table;
    }
} 
