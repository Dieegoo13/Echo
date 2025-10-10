<?php

namespace App\Models;

use MF\Model\Model;

class Echos extends Model
{

    private $id;
    private $id_usuario;
    private $echo;
    private $data;

    public function __get($atributo){
        return $this->$atributo;
    }

    public function __set($atributo, $valor){
        $this->$atributo = $valor;
    }

    public function salvar (){
        $query = "INSERT INTO echos(id_usuario, echo) 
                VALUES (:id_usuario, :echo)"
        ;

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':echo', $this->__get('echo'));
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $this;
    }

    public function getAll(){
        $query = "
            SELECT 
                e.id, 
                e.id_usuario, 
                u.nome, 
                e.echo, 
                DATE_FORMAT(e.data, '%d/%m/%y %H:%i') AS data
            FROM echos AS e
            LEFT JOIN tb_usuarios AS u ON e.id_usuario = u.id
            WHERE e.id_usuario = :id_usuario
            ORDER BY
                e.data DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO:: FETCH_ASSOC);
    }
    
}
