<?php

// Incluir la capa de datos de categorías y la entidad correspondiente
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'CategoriaDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Categoria.php';

class CategoriaNegocio {
    private $categoriaDatos;

    public function __construct() {
        $this->categoriaDatos = new CategoriaDatos();
    }

    // Registrar una categoría con validación prevviamente
    public function registrarCategoria(Categoria $categoria) {
        // Regla de negocio: El nombre de la categoría no puede ir vacío
        if (empty($categoria->getNombreCategoria())) {
            return "El nombre de la categoría es un campo obligatorio.";
        }
        
        return $this->categoriaDatos->insertar($categoria);
    }

    // Modificar una categoría existente 
    public function modificarCategoria(Categoria $categoria) {
        if (empty($categoria->getIdCategoria()) || empty($categoria->getNombreCategoria())) {
            return "Datos insuficientes para actualizar la categoría.";
        }
        
        return $this->categoriaDatos->modificar($categoria);
    }

    // Eliminar una categoría por su ID 
    public function eliminarCategoria($id_categoria) {
        if (empty($id_categoria)) {
            return false;
        }
        return $this->categoriaDatos->eliminar($id_categoria);
    }

    // Listar todas las categorías registradas
    public function listarCategorias() {
        return $this->categoriaDatos->listarTodo();
    }

    // Buscar una categoría específica por su ID
    public function obtenerPorId($id_categoria) {
        if (empty($id_categoria)) {
            return null;
        }
        return $this->categoriaDatos->buscarPorId($id_categoria);
    }
}