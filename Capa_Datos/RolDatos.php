<?php

// Incluir la clase de conexión y la entidad correspondiente
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Rol.php';

class RolDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar un nuevo rol en la base de datos
    public function insertar(Rol $rol) {
        $sql = "INSERT INTO rol (nombre_rol) VALUES (?)";
        // Pasamos los parámetros extrayéndolos de la entidad mediante su getter
        $params = [$rol->getNombreRol()];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para modificar un rol existente
    public function modificar(Rol $rol) {
        $sql = "UPDATE rol SET nombre_rol = ? WHERE id_rol = ?";
        $params = [
            $rol->getNombreRol(),
            $rol->getIdRol()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para eliminar un rol por su ID
    public function eliminar($id_rol) {
        $sql = "DELETE FROM rol WHERE id_rol = ?";
        $params = [$id_rol];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar todos los roles (Devuelve un array de objetos Rol)
    public function listar todos() {
        $sql = "SELECT id_rol, nombre_rol FROM rol";
        $filas = $this->conexion->get_records($sql);
        
        $listaRoles = [];
        // Mapeamos los resultados de la base de datos a objetos de nuestra entidad
        foreach ($filas as $fila) {
            $listaRoles[] = new Rol($fila['id_rol'], $fila['nombre_rol']);
        }
        return $listaRoles;
    }

    // Método para buscar un rol específico por su ID
    public function buscarPorId($id_rol) {
        $sql = "SELECT id_rol, nombre_rol FROM rol WHERE id_rol = ?";
        $fila = $this->conexion->get_record($sql, [$id_rol]);
        
        if ($fila) {
            return new Rol($fila['id_rol'], $fila['nombre_rol']);
        }
        return null;
    }
}