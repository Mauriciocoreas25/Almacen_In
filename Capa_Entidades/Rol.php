<?php

class Rol {
    // Atributos privados
    private $id_rol;
    private $nombre_rol;

    // Constructor para inicializar la entidad
    public function __construct($id_rol = null, $nombre_rol = "") {
        $this->id_rol = $id_rol;
        $this->nombre_rol = $nombre_rol;
    }

    // --- MÉTODOS GETTER Y SETTER ---

    // id_rol
    public function getIdRol() {
        return $this->id_rol;
    }

    public function setIdRol($id_rol) {
        $this->id_rol = $id_rol;
    }

    // nombre_rol
    public function getNombreRol() {
        return $this->nombre_rol;
    }

    public function setNombreRol($nombre_rol) {
        $this->nombre_rol = $nombre_rol;
    }
}