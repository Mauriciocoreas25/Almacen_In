<?php

class DetalleVenta {
    // Atributos privados 
    private $id_detalle;
    private $id_venta;
    private $id_producto;
    private $cantidad;
    private $precio_unitario;

    // Constructor para inicializar la entidad
    public function __construct($id_detalle = null, $id_venta = null, $id_producto = null, $cantidad = 0, $precio_unitario = 0.0) {
        $this->id_detalle = $id_detalle;
        $this->id_venta = $id_venta;
        $this->id_producto = $id_producto;
        $this->cantidad = (int)$cantidad;
        $this->precio_unitario = (double)$precio_unitario;
    }

    // --- MÉTODOS GETTER Y SETTER ---

    // id_detalle
    public function getIdDetalle() {
        return $this->id_detalle;
    }

    public function setIdDetalle($id_detalle) {
        $this->id_detalle = $id_detalle;
    }

    // id_venta
    public function getIdVenta() {
        return $this->id_venta;
    }

    public function setIdVenta($id_venta) {
        $this->id_venta = $id_venta;
    }

    // id_producto
    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }

    // cantidad
    public function getCantidad() {
        return $this->cantidad;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = (int)$cantidad;
    }

    // precio_unitario
    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario($precio_unitario) {
        $this->precio_unitario = (double)$precio_unitario;
    }
}