<?php

class Categoria {
    // Atributos privados
    private $id_categoria; // privado para que solo se pueda acceder a  traves de los metodos getter  y setter 
    private $nombre_categoria; t// privado para que solo se  pueda acceder a  traves de  los metodos  getter   y SETTER

    // Constructor para inicializar la entidad cuando se cree un nuevo  objeto de  la calse categoria  
    public function __construct($id_categoria = null, $nombre_categoria = "") {  
        $this->id_categoria = $id_categoria;
        $this->nombre_categoria = $nombre_categoria;
    }

    // --- MÉTODOS GETTER Y SETTER para acceder a los atributos que son privados ---

    // id_categoria
    public function getIdCategoria() {
        return $this->id_categoria;
    }

    public function setIdCategoria($id_categoria) {
        $this->id_categoria = $id_categoria;
    }

    // nombre_categoria
    public function getNombreCategoria() {
        return $this->nombre_categoria;
    }

    public function setNombreCategoria($nombre_categoria) {
        $this->nombre_categoria = $nombre_categoria;
    }
}