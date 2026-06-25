<?php

// Incluir la clase de conexión y las entidades necesarias
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Usuario.php';

class UsuarioDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar un nuevo usuario 
    public function insertar(Usuario $usuario) {
        $sql = "INSERT INTO usuario (id_rol, usuario, nombre, apellidos, tipo_documento, num_documento, direccion, telefono, email, clave, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Convertimos el estado explícitamente a un entero 1 o 0 para que MySQL no nos de problemas con el tipo de dato.
        $estadoEntero = $usuario->getEstado() ? 1 : 0;

        $params = [
            $usuario->getIdRol(),
            $usuario->getUsername(), 
            $usuario->getNombreComplete(), 
            '', // Apellidos  lo podemos dejar vacio ya que no lo  pedimos en el formulario.
            'DUI',
            'DUI-' . time(), // Valor temporal único por los campos UNIQUE si no los pides en el formulario
            '',
            '',
            $usuario->getUsername() . '@correo.com', // Correo dinámico temporal para evitar el UNIQUE de la BD
            $usuario->getPassword(), 
            $estadoEntero //AQUÍ: Le pasamos el entero limpio que representa el estado ya sea 1 (activo) o 0 (inactivo)
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para modificar los datos de un usuario existente
    public function modificar(Usuario $usuario) {
        $sql = "UPDATE usuario SET id_rol = ?, usuario = ?, nombre = ?, clave = ?, estado = ? WHERE id_usuario = ?";
        $params = [
            $usuario->getIdRol(),
            $usuario->getUsername(),
            $usuario->getNombreComplete(),
            $usuario->getPassword(),
            $usuario->getEstado() ? 1 : 0,
            $usuario->getIdUsuario()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para cambiar el estado (activar/desactivar) de un usuario
    public function cambiarEstado($id_usuario, $estado) {
        $sql = "UPDATE usuario SET estado = ? WHERE id_usuario = ?";
        $params = [$estado ? 1 : 0, $id_usuario];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar todos los usuarios (Mapeado exacto al constructor)
    public function listarTodo() {
        $sql = "SELECT id_usuario, usuario, clave, nombre, id_rol, estado FROM usuario";
        $filas = $this->conexion->get_records($sql);
        
        $listaUsuarios = [];
        foreach ($filas as $fila) {
            $listaUsuarios[] = new Usuario(
                $fila['id_usuario'],
                $fila['usuario'],      // 2. username
                $fila['clave'],        // 3. password
                $fila['nombre'],       // 4. nombre_complete
                $fila['id_rol'],       // 5. id_rol
                (bool)$fila['estado']  // 6. estado
            );
        }
        return $listaUsuarios;
    }

    // Método para buscar un usuario por su ID
    public function buscarPorId($id_usuario) {
        $sql = "SELECT id_usuario, usuario, clave, nombre, id_rol, estado FROM usuario WHERE id_usuario = ?";
        $fila = $this->conexion->get_record($sql, [$id_usuario]);
        
        if ($fila) {
            return new Usuario(
                $fila['id_usuario'],
                $fila['usuario'],      // 2. username
                $fila['clave'],        // 3. password
                $fila['nombre'],       // 4. nombre_complete
                $fila['id_rol'],       // 5. id_rol
                (bool)$fila['estado']  // 6. estado
            );
        }
        return null;
    }

    // --- EL PUENTE DEL LOGIN ---
    public function buscarPorUsuario($username) {
        $sql = "SELECT id_usuario, usuario, clave, nombre, id_rol, estado FROM usuario WHERE usuario = ?";
        $fila = $this->conexion->get_record($sql, [$username]);
        
        if ($fila) {
            // EL ORDEN EXACTO DEL  CONSTRUCTOR EN USUARIO.PHP
            return new Usuario(
                $fila['id_usuario'],   // 1. id_usuario
                $fila['usuario'],      // 2. username
                $fila['clave'],        // 3. password
                $fila['nombre'],       // 4. nombre_complete
                $fila['id_rol'],       // 5. id_rol
                (bool)$fila['estado']  // 6. estado
            );
        }
        return null;
    }
}