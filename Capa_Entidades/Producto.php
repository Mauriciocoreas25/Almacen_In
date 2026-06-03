<?php

class Producto {
    // Atributos privados 
    private $id_producto;
    private $codigo;
    private $nombre;
    private $id_categoria;
    private $precio;
    private $stock;
    private $estado;

    // Constructor para inicializar la entidad
    public function __construct($id_producto = null, $codigo = "", $nombre = "", $id_categoria = null, $precio = 0.0, $stock = 0, $estado = true) {
        $this->id_producto = $id_producto;
        $this->codigo = $codigo;
        $this->nombre = $nombre;
        $this->id_categoria = $id_categoria;
        $this->precio = (double)$precio;
        $this->stock = (int)$stock;
        $this->estado = $estado;
    }

    // --- MÉTODOS GETTER Y SETTER ---

    // id_producto
    public function getIdProducto() {
        return $this->id_producto;
    }

    public function setIdProducto($id_producto) {
        $this->id_producto = $id_producto;
    }

    // codigo
    public function getCodigo() {
        return $this->codigo;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
    }

    // nombre
    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    // id_categoria
    public function getIdCategoria() {
        return $this->id_categoria;
    }

    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }

    // precio
    public function getPrecio() {
        return $this->precio;
    }

    public function setPrecio($precio) {
        $this->precio = (double)$precio;
    }

    // stock
    public function getStock() {
        return $this->stock;
    }

    public function setStock($stock) {
        $this->stock = (int)$stock;
    }

    // estado
    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }
}