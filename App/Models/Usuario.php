<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model
{

    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function salvar()
    {
        $query = "INSERT INTO tb_usuarios (nome, email, senha) 
          VALUES (:nome, :email, :senha)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $this;
    }

    public function validarCadastro()
    {

        $valido = true;

        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('email')) < 3 || !str_contains($this->__get('email'), '@')) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }


    public function getUsuarioPorEmail()
    {

        $query = "SELECT nome, email FROM tb_usuarios where email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar()
    {

        $query = "SELECT id, nome, email
                FROM tb_usuarios
                WHERE email = :email AND senha = :senha ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));

        $stmt->execute();

        $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($resultado) {
            $this->__set('id', $resultado['id']);
            $this->__set('nome', $resultado['nome']);
        } else {
            $this->__set('id', null);
            $this->__set('nome', null);
        }

        return $this;
    }


    public function getAll()
    {
        $query = "
            SELECT 
                u.id, 
                u.nome, 
                u.email,
                (
                    SELECT 
                        COUNT(*) 
                    FROM 
                        usuarios_seguidores AS us
                    WHERE 
                        us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                        AND us.id_usuario_seguindo = u.id
                ) AS seguindo_sn
            FROM 
                tb_usuarios AS u
            WHERE 
                u.nome LIKE :nome 
                AND u.id != :id_usuario
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%' . $this->__get('nome') . '%');
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function seguirUsuario($id_usuario_seguindo){

        $query = "
            INSERT INTO usuarios_seguidores (id_usuario, id_usuario_seguindo)
            VALUES (:id_usuario, :id_usuario_seguindo)
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);

        $stmt->execute();

        return true;

    }

    public function deixarSeguirUsuario($id_usuario_seguindo){

        $query = "
            DELETE FROM usuarios_seguidores
            WHERE id_usuario = :id_usuario
            AND id_usuario_seguindo = :id_usuario_seguindo
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo', $id_usuario_seguindo);

        $stmt->execute();

        return true;
    }
    
}
