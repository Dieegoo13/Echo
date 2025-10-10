<?php 

namespace App\Controllers;

//os recursos do miniframework

use MF\Controller\Action;

class AppController extends Action {


    public function timeline(){

        session_start();

        echo "chegamos aq";

        if(!empty($_SESSION['id']) && !empty($_SESSION['nome'])){
            $this->render('timeline');
        }else{
            header('Location: /?login=erro');
        }


    }

}

?>