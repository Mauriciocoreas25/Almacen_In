<?php

// Incluir la clase de conexión y la entidad Cliente
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Cliente.php';

class ClienteDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar un nuevo cliente
    public function insertar(Cliente $cliente) {
        $sql = "INSERT INTO cliente (nombre_completo, telefono, correo, direccion) VALUES (?, ?, ?, ?)";
        $params = [
            $cliente->getNombreCompleto(),
            $cliente->getTelefono(),
            $cliente->getCorreo(),
            $cliente->getDireccion()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para modificar los datos de un cliente si existe 
    public function modificar(Cliente $cliente) {
        $sql = "UPDATE cliente SET nombre_completo = ?, telefono = ?, correo = ?, direccion = ? WHERE id_cliente = ?";
        $params = [
            $cliente->getNombreCompleto(),
            $cliente->getTelefono(),
            $cliente->getCorreo(),
            $cliente->getDireccion(),
            $cliente->getIdCliente()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para eliminar un cliente de la base de datos
    public function eliminar($id_cliente) {
        $sql = "DELETE FROM cliente WHERE id_cliente = ?";
        $params = [$id_cliente];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar todos los clientes registrados
    public function listarTodo() {
        $sql = "SELECT id_cliente, nombre_completo, telefono, correo, direccion FROM cliente";
        $filas = $this->conexion->get_records($sql);
        
        $listaClientes = [];
        foreach ($filas as $fila) {
            $listaClientes[] = new Cliente(
                $fila['id_cliente'],
                $fila['nombre_completo'],
                $fila['telefono'],
                $fila['correo'],
                $fila['direccion']
            );
        }
        return $listaClientes;
    }

    // Método para buscar un cliente específico por su ID
    public function buscarPorId($id_cliente) {
        $sql = "SELECT id_cliente, nombre_completo, telefono, correo, direccion FROM cliente WHERE id_cliente = ?";
        $fila = $this->conexion->get_record($sql, [$id_cliente]);
        
        if ($fila) {
            return new Cliente(
                $fila['id_cliente'],
                $fila['nombre_completo'],
                $fila['telefono'],
                $fila['correo'],
                $fila['direccion']
            );
        }
        return null;
    }

    // Método para buscar clientes por nombre 
    public function buscarPorNombre($busqueda) {
        $sql = "SELECT id_cliente, nombre_completo, telefono, correo, direccion FROM cliente WHERE nombre_completo LIKE ?";
        $params = ["%" . $busqueda . "%"];
        $filas = $this->conexion->get_records($sql, $params);
        
        $listaClientes = [];
        foreach ($filas as $fila) {
            $listaClientes[] = new Cliente(
                $fila['id_cliente'],
                $fila['nombre_completo'],
                $fila['telefono'],
                $fila['correo'],
                $fila['direccion']
            );
        }
        return $listaClientes;
    }
}