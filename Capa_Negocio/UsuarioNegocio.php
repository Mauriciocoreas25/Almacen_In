<?php

// Incluir la capa de datos de usuarios y la entidad
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'UsuarioDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Usuario.php';

class UsuarioNegocio {
    private $usuarioDatos;

    public function __construct() {
        $this->usuarioDatos = new UsuarioDatos();
    }

    // Método para registrar un usuario aplicando encriptación de contraseña (RF-04)
    public function registrarUsuario(Usuario $usuario) {
        // Regla de negocio: La contraseña nunca se guarda en texto plano
        if (!empty($usuario->getPassword())) {
            $passwordEncriptada = password_hash($usuario->getPassword(), PASSWORD_BCRYPT);
            $usuario->setPassword($passwordEncriptada);
        }
        return $this->usuarioDatos->insertar($usuario);
    }

    // Método para modificar datos del usuario (RF-04)
    public function modificarUsuario(Usuario $usuario) {
        $usuarioExistente = $this->usuarioDatos->buscarPorId($usuario->getIdUsuario());
        
        if (!$usuarioExistente) {
            return false;
        }

        // Si el usuario dejó la contraseña en blanco en la interfaz, conservamos la que ya tenía
        if (empty($usuario->getPassword())) {
            $usuario->setPassword($usuarioExistente->getPassword());
        } else {
            // Si escribió una nueva, la encriptamos
            $passwordEncriptada = password_hash($usuario->getPassword(), PASSWORD_BCRYPT);
            $usuario->setPassword($passwordEncriptada);
        }

        return $this->usuarioDatos->modificar($usuario);
    }

    // Cambiar estado (Activar/Desactivar) - RF-04
    public function cambiarEstadoUsuario($id_usuario, $estado) {
        if (empty($id_usuario)) {
            return false;
        }
        return $this->usuarioDatos->cambiarEstado($id_usuario, $estado);
    }

    // Listar todos los usuarios para el módulo de administración
    public function listarUsuarios() {
        return $this->usuarioDatos->listarTodo();
    }

    // Buscar un usuario específico por su ID
    public function obtenerPorId($id_usuario) {
        return $this->usuarioDatos->buscarPorId($id_usuario);
    }

    // --- MÉTODO CRÍTICO: VALIDACIÓN DE INICIO DE SESIÓN 
    public function iniciarSesion($username, $password) {
        // 1. Validar que los campos no vengan vacíos
        if (empty($username) || empty($password)) {
            return "Por favor, complete todos los campos.";
        }

        // 2. Buscar al usuario en la base de datos por su string de cuenta
        $usuario = $this->usuarioDatos->buscarPorUsuario($username);

        // 3. Verificar si el usuario existe
        if (!$usuario) {
            return "Las credenciales ingresadas son incorrectas.";
        }

        // 4. Verificar si el usuario está activo en el sistema
        if (!$usuario->getEstado()) {
            return "Este usuario se encuentra desactivado. Contacte al administrador.";
        }

        // 5. Verificar si la contraseña coincide con el hash encriptado de la BD
        if (password_verify($password, $usuario->getPassword())) {
            // Credenciales correctas. Iniciamos la sesión nativa de PHP 
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            // Guardamos los datos clave en la sesión para el control de acceso global
            $_SESSION['id_usuario'] = $usuario->getIdUsuario();
            $_SESSION['nombre_usuario'] = $usuario->getNombreComplete();
            $_SESSION['user_cuenta'] = $usuario->getUsername();
            $_SESSION['id_rol'] = $usuario->getIdRol();
            
            return true; // Login exitoso
        }

        return "Las credenciales ingresadas son incorrectas.";
    }
}