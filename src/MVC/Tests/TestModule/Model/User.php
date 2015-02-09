<?php 

namespace MVC\Tests\TestModule\Model;

use MVC\DataBase\Model,
    MVC\DataBase\PDO;

/**
 * Example of User Model
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class User extends Model
{

    /**
     * Construct of the class
     *
     * @access public
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo, 'Usuario');
    }

}
	