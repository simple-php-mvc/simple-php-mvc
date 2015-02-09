<?php

namespace MVC\Tests\TestModule\Controller;

use MVC\Controller\Controller,
    MVC\Tests\TestModule\Model\User,
    MVC\MVC;

/**
 * User Controller Example
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class UserController extends Controller
{

    /**
     * Example of index action for someone route with the use of the a User model
     * Using the render view
     * 
     * @access public
     * @param MVC $mvc
     * @return string
     * @throws \LogicException
     */
    public function index(MVC $mvc)
    {
        if (!$mvc->hasCvpp('pdo')) {
            throw new \LogicException('PDO don\'t exists. Register the MVC\DataBase\PdoProvider for use this function.');
        }
        $userModel = new User($mvc->getCvpp('pdo'));
        $users = $userModel->findAll();
        
        return $mvc->view()->render('User/index.html', array(
            'users' => $users
        ));
    }

}
