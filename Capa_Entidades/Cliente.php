<?php

class Cliente {
    // Atributos privados para  encapsular los datos del cliente 
    private $id_cliente;
    private $nombre_completo;
    private $telefono;
    private $correo;
    private $direccion;

    // Constructor para inicializar la entidad cliente 
    public function __construct($id_cliente = null, $nombre_completo = "", $telefono = "", $correo = "", $direccion = "") {
        $this->id_cliente = $id_cliente;
        $this->nombre_completo = $nombre_completo;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
    }

    // --- MÉTODOS GETTER Y SETTER  PARA OBTENER LOS VALORES ---

    // id_cliente
    public function getIdCliente() {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    // nombre_completo
    public function getNombreCompleto() {
        return $this->nombre_completo;
    }

    public function setNombreCompleto($nombre_completo) {
        $this->nombre_completo = $nombre_completo;
    }

    // telefono
    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
    }

    // correo
    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    // direccion
    public function getDireccion() {
        return $this->direccion;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
    }
}