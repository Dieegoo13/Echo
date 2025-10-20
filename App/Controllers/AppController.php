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

        // Model Echos → para listar os posts
        $echo = Container::getModel('Echos');
        $echo->__set('id_usuario', $_SESSION['id']);
        $echos = $echo->getAll();
        $this->view->echos = $echos;

        // Model Usuario → para informações do perfil
        $usuario = Container::getModel('Usuario'); // ← CORRIGIDO AQUI
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUser();
        $this->view->total_echos = $usuario->getTotalEchos();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

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

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id_usuario', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUser() ?: [];
        $this->view->total_echos = $usuario->getTotalEchos() ?: 0;
        $this->view->total_seguindo = $usuario->getTotalSeguindo() ?: 0;
        $this->view->total_seguidores = $usuario->getTotalSeguidores() ?: 0;

        $usuarios = [];

        if (!empty($pesquisarPor)) {
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


    public function remover()
    {
        $this->validaAutenticacao();

        $id_echo = isset($_GET['id']) ? $_GET['id'] : '';

        if(!empty($id_echo)){
            $echos = Container::getModel('Echos');
            $echos->__set('id_usuario', $_SESSION['id']);
            $echos->__set('id', $id_echo);
            $echos->removerEcho();
        }

        header('Location: /timeline');
    }


    public function acao()
    {
        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario-> __set ('id', $_SESSION['id']);

        if($acao == 'seguir'){
            $usuario->seguirUsuario($id_usuario_seguindo);
        }else if($acao == 'deixar_de_seguir'){
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
    }


    

    
}
