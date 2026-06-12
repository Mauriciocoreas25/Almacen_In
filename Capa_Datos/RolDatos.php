<?php

// Incluir la clase de conexión y la entidad correspondiente
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Rol.php';

/**
 * RolDatos — Adaptado al schema real de la BD.
 *
 * Schema real tabla 'rol':
 *   id_rol, nombre (varchar 30), descripcion, estado (bit)
 *
 * La entidad Rol.php usa getNombreRol() → mapeado a columna 'nombre'
 */
class RolDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar nuevo rol
    public function insertar(Rol $rol) {
        $sql    = "INSERT INTO rol (nombre) VALUES (?)";
        $params = [$rol->getNombreRol()];
        return $this->conexion->execute_query($sql, $params);
    }

    // Modificar rol existente
    public function modificar(Rol $rol) {
        $sql    = "UPDATE rol SET nombre = ? WHERE id_rol = ?";
        $params = [$rol->getNombreRol(), $rol->getIdRol()];
        return $this->conexion->execute_query($sql, $params);
    }

    // Eliminar rol por ID
    public function eliminar($id_rol) {
        $sql    = "DELETE FROM rol WHERE id_rol = ?";
        $params = [$id_rol];
        return $this->conexion->execute_query($sql, $params);
    }

    // Listar todos los roles
    public function listarTodo() {
        $sql  = "SELECT id_rol, nombre FROM rol ORDER BY id_rol ASC";
        $filas = $this->conexion->get_records($sql);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Rol($fila['id_rol'], $fila['nombre']);
        }
        return $lista;
    }

    // Buscar rol por ID
    public function buscarPorId($id_rol) {
        $sql  = "SELECT id_rol, nombre FROM rol WHERE id_rol = ?";
        $fila = $this->conexion->get_record($sql, [$id_rol]);

        if ($fila) {
            return new Rol($fila['id_rol'], $fila['nombre']);
        }
        return null;
    }
}