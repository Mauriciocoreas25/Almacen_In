<?php

class DetalleVenta {
    // Atributos privados 
    private $id_detalle;
    private $id_venta;
    private $id_producto;
    private $cantidad;
    private $precio_unitario;
    private $subtotal;

    // Constructor para inicializar la entidad
    public function __construct($id_detalle = null, $id_venta = null, $id_producto = null, $cantidad = 0, $precio_unitario = 0.0, $subtotal = null) {
        $this->id_detalle = $id_detalle;
        $this->id_venta = $id_venta;
        $this->id_producto = $id_producto;
        $this->cantidad = (int)$cantidad;
        $this->precio_unitario = (double)$precio_unitario;
        $this->subtotal = $subtotal !== null ? (double)$subtotal : round((int)$cantidad * (double)$precio_unitario, 2);
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
        $this->recalcularSubtotal();
    }

    // precio_unitario
    public function getPrecioUnitario() {
        return $this->precio_unitario;
    }

    public function setPrecioUnitario($precio_unitario) {
        $this->precio_unitario = (double)$precio_unitario;
        $this->recalcularSubtotal();
    }

    // subtotal
    public function getSubtotal() {
        return $this->subtotal;
    }

    public function setSubtotal($subtotal) {
        $this->subtotal = (double)$subtotal;
    }

    private function recalcularSubtotal() {
        $this->subtotal = round($this->cantidad * $this->precio_unitario, 2);
    }
}