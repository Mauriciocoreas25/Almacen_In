<?php

class Cliente {
    // Atributos privados para  encapsular los datos del cliente 
    private $id_cliente;
    private $nombre_completo;
    private $telefono;
    private $correo;
    private $direccion;
    private $dui;
    private $nit;
    private $personalidad_juridica;
    private $nrc;
    private $giro;

    // Constructor para inicializar la entidad cliente 
    public function __construct($id_cliente = null, $nombre_completo = "", $telefono = "", $correo = "", $direccion = "", $dui = null, $nit = null, $personalidad_juridica = 0, $nrc = null, $giro = null) {
        $this->id_cliente = $id_cliente;
        $this->nombre_completo = $nombre_completo;
        $this->telefono = $telefono;
        $this->correo = $correo;
        $this->direccion = $direccion;
        $this->dui = $dui;
        $this->nit = $nit;
        $this->personalidad_juridica = $personalidad_juridica;
        $this->nrc = $nrc;
        $this->giro = $giro;
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

    // dui
    public function getDui() {
        return $this->dui;
    }

    public function setDui($dui) {
        $this->dui = $dui;
    }

    // nit
    public function getNit() {
        return $this->nit;
    }

    public function setNit($nit) {
        $this->nit = $nit;
    }

    // personalidad_juridica
    public function getPersonalidadJuridica() {
        return $this->personalidad_juridica;
    }

    public function setPersonalidadJuridica($personalidad_juridica) {
        $this->personalidad_juridica = $personalidad_juridica;
    }

    // nrc
    public function getNrc() {
        return $this->nrc;
    }

    public function setNrc($nrc) {
        $this->nrc = $nrc;
    }

    // giro
    public function getGiro() {
        return $this->giro;
    }

    public function setGiro($giro) {
        $this->giro = $giro;
    }
}