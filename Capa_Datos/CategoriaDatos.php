<?php

// Incluimos la clase de conexión y la entidad Categoria
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Categoria.php';

/**
 * CategoriaDatos — Adaptado al schema real de la BD.
 *
 * Schema real tabla 'categoria':
 *   id_categoria, nombre (varchar 50), descripcion, estado (bit)
 *
 * La entidad Categoria.php usa: getNombreCategoria() y getDescripcion()
 * Mapeo: 'nombre' ↔ 'nombre_categoria'
 */
class CategoriaDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar nueva categoría
    public function insertar(Categoria $categoria) {
        $sql    = "INSERT INTO categoria (nombre, descripcion) VALUES (?, ?)";
        $params = [
            $categoria->getNombreCategoria(),
            $categoria->getDescripcion() ?? ''
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Modificar categoría existente
    public function modificar(Categoria $categoria) {
        $sql    = "UPDATE categoria SET nombre = ?, descripcion = ? WHERE id_categoria = ?";
        $params = [
            $categoria->getNombreCategoria(),
            $categoria->getDescripcion() ?? '',
            $categoria->getIdCategoria()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Eliminar categoría
    public function eliminar($id_categoria) {
        $sql    = "DELETE FROM categoria WHERE id_categoria = ?";
        $params = [$id_categoria];
        return $this->conexion->execute_query($sql, $params);
    }

    // Verificar si la categoría tiene productos asociados
    public function tieneProductosAsociados($id_categoria) {
        $sql  = "SELECT COUNT(*) as total FROM producto WHERE id_categoria = ?";
        $fila = $this->conexion->get_record($sql, [$id_categoria]);
        return $fila && (int)$fila['total'] > 0;
    }

    // Listar todas las categorías
    public function listarTodo() {
        $sql  = "SELECT id_categoria, nombre, descripcion FROM categoria ORDER BY id_categoria ASC";
        $filas = $this->conexion->get_records($sql);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Categoria(
                $fila['id_categoria'],
                $fila['nombre'],          // → getNombreCategoria()
                $fila['descripcion'] ?? ''
            );
        }
        return $lista;
    }

    // Buscar categoría por ID
    public function buscarPorId($id_categoria) {
        $sql  = "SELECT id_categoria, nombre, descripcion FROM categoria WHERE id_categoria = ?";
        $fila = $this->conexion->get_record($sql, [$id_categoria]);

        if ($fila) {
            return new Categoria(
                $fila['id_categoria'],
                $fila['nombre'],
                $fila['descripcion'] ?? ''
            );
        }
        return null;
    }
}