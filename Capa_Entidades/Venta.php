<?php

class Venta {
    // Atributos privados 
    private $id_venta;
    private $fecha_venta;
    private $id_cliente;
    private $id_usuario;
    private $subtotal;
    private $iva;
    private $total;

    // Constructor para inicializar la entidad
    public function __construct($id_venta = null, $fecha_venta = null, $id_cliente = null, $id_usuario = null, $subtotal = 0.0, $iva = 0.0, $total = 0.0) {
        $this->id_venta = $id_venta;
        $this->fecha_venta = $fecha_venta;
        $this->id_cliente = $id_cliente;
        $this->id_usuario = $id_usuario;
        $this->subtotal = (double)$subtotal;
        $this->iva = (double)$iva;
        $this->total = (double)$total;
    }

    // --- MÉTODOS GETTER Y SETTER ---

    // id_venta
    public function getIdVenta() {
        return $this->id_venta;
    }

    public function setIdVenta($id_venta) {
        $this->id_venta = $id_venta;
    }

    // fecha_venta
    public function getFechaVenta() {
        return $this->fecha_venta;
    }

    public function setFechaVenta($fecha_venta) {
        $this->fecha_venta = $fecha_venta;
    }

    // id_cliente
    public function getIdCliente() {
        return $this->id_cliente;
    }

    public function setIdCliente($id_cliente) {
        $this->id_cliente = $id_cliente;
    }

    // id_usuario
    public function getIdUsuario() {
        return $this->id_usuario;
    }

    public function setIdUsuario($id_usuario) {
        $this->id_usuario = $id_usuario;
    }

    // subtotal
    public function getSubtotal() {
        return $this->subtotal;
    }

    public function setSubtotal($subtotal) {
        $this->subtotal = (double)$subtotal;
    }

    // iva
    public function getIva() {
        return $this->iva;
    }

    public function setIva($iva) {
        $this->iva = (double)$iva;
    }

    // total
    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = (double)$total;
    }
}