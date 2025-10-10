<?php

namespace App\Controllers;

//os recursos do miniframework

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{


    public function timeline()
    {

        $this->validaAutenticacao();

        $echo = Container::getModel('Echos');

        $echo->__set('id_usuario', $_SESSION['id']);

        $echos = $echo->getAll();


        $this->view->echos = $echos;

        $this->render('timeline');

    }

    public function echos()
    {

        $this->validaAutenticacao();

        $echos = Container::getModel('Echos');

        $echos->__set('echo', $_POST['echo']);
        $echos->__set('id_usuario', $_SESSION['id']);

        $echos->salvar();

        header('Location: /timeline');
        exit;
    }


    public function quemSeguir()
    {

        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = [];

        if (!empty($pesquisarPor)) {
            
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuarios = $usuario->getAll();

        }

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');

    }

    public function validaAutenticacao()
    {
        session_start();

        if (!isset($_SESSION['id']) || !isset($_SESSION['nome']) || empty($_SESSION['nome'])) {

            header('Location: /?login=erro');
            exit;
        }
    }
}
