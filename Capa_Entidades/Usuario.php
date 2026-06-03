<?php

class Usuario {
    // Atributos privados 
    private $id_usuario;
    private $username;
    private $password;
    private $nombre_complete;
    private $id_rol;
    private $estado;

    // Constructor para inicializar la entidad
    public function __construct($id_usuario = null, $username = "", $password = "", $nombre_complete = "", $id_rol = null, $estado = true) {
        $this->id_usuario = $id_usuario;
        $this->username = $username;
        $this->password = $password;
        $this->nombre_complete = $nombre_complete;
        $this->id_rol = $id_rol;
        $this->estado = $estado;
    }

    // --- MÉTODOS GETTER Y SETTER ---

    // id_usuario
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    // username
    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    // password
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    // nombre_complete
    public function getNombreComplete() {
        return $this->nombre_complete;
    }

    public function setNombreComplete($nombre_complete) {
        $this->nombre_complete = $nombre_complete;
    }

    // id_rol
    public function getIdRol() {
        return $this->id_rol;
    }

    public function setIdRol($id_rol) {
        $this->id_rol = $id_rol;
    }

    // estado
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}