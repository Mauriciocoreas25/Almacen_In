<?php

// Incluir la capa de datos de clientes y la entidad correspondiente
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'ClienteDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Cliente.php';

class ClienteNegocio {
    private $clienteDatos;

    public function __construct() {
        $this->clienteDatos = new ClienteDatos();
    }

    // Registrar un cliente con validaciones previas
    public function registrarCliente(Cliente $cliente) {
        // Regla de negocio: Validar campos obligatorios mínimos
        if (empty($cliente->getNombreCompleto()) || empty($cliente->getTelefono())) {
            return "El nombre completo y el teléfono son campos obligatorios.";
        }
        
        return $this->clienteDatos->insertar($cliente);
    }

    // Modificar un cliente existente con validaciones 
    public function modificarCliente(Cliente $cliente) {
        if (empty($cliente->getIdCliente()) || empty($cliente->getNombreCompleto()) || empty($cliente->getTelefono())) {
            return "Datos insuficientes para actualizar el registro del cliente.";
        }
        
        return $this->clienteDatos->modificar($cliente);
    }

    // Eliminar un cliente por su ID
    public function eliminarCliente($id_cliente) {
        if (empty($id_cliente)) {
            return false;
        }
        // No se puede eliminar el cliente con ID 1 porque es el Consumidor Final predeterminado
        if ($id_cliente === 1) {
            return "No se puede eliminar el cliente 'Consumidor Final' ya que es requerido por el sistema.";
        }
        if ($this->clienteDatos->tieneVentasAsociadas($id_cliente)) {
            return "No se puede eliminar el cliente porque está asociado a una o más ventas registradas.";
        }
        return $this->clienteDatos->eliminar($id_cliente);
    }

    // Listar todos los clientes registrados
    public function listarClientes() {
        return $this->clienteDatos->listarTodo();
    }

    // Buscar un cliente específico por su ID
    public function obtenerPorId($id_cliente) {
        if (empty($id_cliente)) {
            return null;
        }
        return $this->clienteDatos->buscarPorId($id_cliente);
    }

    // Buscar clientes por coincidencia de nombre 
    public function buscarClientes($busqueda) {
        if (empty($busqueda)) {
            return $this->clienteDatos->listarTodo();
        }
        return $this->clienteDatos->buscarPorNombre($busqueda);
    }
}