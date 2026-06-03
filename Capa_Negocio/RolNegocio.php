<?php

// Incluir la capa de datos operativa correspondientes
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'RolDatos.php';

class RolNegocio {
    private $rolDatos;

    // Constructor: Inicializa la capa de datos para poder usar sus métodos
    public function __construct() {
        $this->rolDatos = new RolDatos();
    }

    // Retorna todos los roles registrados
    public function listarTodo() {
        return $this->rolDatos->listarTodo();
    }

    // Busca un rol específico por su ID
    public function buscarPorId($id_rol) {
        if (empty($id_rol)) {
            return null;
        }
        return $this->rolDatos->buscarPorId($id_rol);
    }
}