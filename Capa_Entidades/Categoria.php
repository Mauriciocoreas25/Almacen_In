<?php

class Categoria {
    // Atributos privados
    private $id_categoria;
    private $nombre_categoria;
    private $descripcion;

    // Constructor
    public function __construct($id_categoria = null, $nombre_categoria = "", $descripcion = "") {
        $this->id_categoria     = $id_categoria;
        $this->nombre_categoria = $nombre_categoria;
        $this->descripcion      = $descripcion;
    }

    // id_categoria
    public function getIdCategoria() { return $this->id_categoria; }
    public function setIdCategoria($id_categoria) { $this->id_categoria = $id_categoria; }

    // nombre_categoria
    public function getNombreCategoria() { return $this->nombre_categoria; }
    public function setNombreCategoria($nombre_categoria) { $this->nombre_categoria = $nombre_categoria; }

    // descripcion
    public function getDescripcion() { return $this->descripcion; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
}