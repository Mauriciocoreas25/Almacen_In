<?php

// Incluimos la clase de conexión y la entidad Categoria
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Categoria.php';

class CategoriaDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar una nueva categoría en la base de datos del sistema
    public function insertar(Categoria $categoria) {
        $sql = "INSERT INTO categoria (nombre_categoria) VALUES (?)";
        $params = [$categoria->getNombreCategoria()];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para modificar una categoría existente en la base de datos del sistema
    public function modificar(Categoria $categoria) {
        $sql = "UPDATE categoria SET nombre_categoria = ? WHERE id_categoria = ?"; 
        $params = [
            $categoria->getNombreCategoria(),
            $categoria->getIdCategoria()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para eliminar una categoría de la base de datos del sistema
    public function eliminar($id_categoria) {
        $sql = "DELETE FROM categoria WHERE id_categoria = ?";
        $params = [$id_categoria];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar todas las categorías en la base de datos del sistema
    public function listarTodo() {
        $sql = "SELECT id_categoria, nombre_categoria FROM categoria";
        $filas = $this->conexion->get_records($sql);
        
        $listaCategorias = [];
        foreach ($filas as $fila) {
            $listaCategorias[] = new Categoria(
                $fila['id_categoria'],
                $fila['nombre_categoria']
            );
        }
        return $listaCategorias;
    }

    // Método para buscar una categoría específica por su ID
    public function buscarPorId($id_categoria) {
        $sql = "SELECT id_categoria, nombre_categoria FROM categoria WHERE id_categoria = ?";
        $fila = $this->conexion->get_record($sql, [$id_categoria]);
        
        if ($fila) {
            return new Categoria(
                $fila['id_categoria'],
                $fila['nombre_categoria']
            );
        }
        return null;
    }
}