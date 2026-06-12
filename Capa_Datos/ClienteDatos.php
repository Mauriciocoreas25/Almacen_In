<?php

// Incluir la clase de conexión y la entidad Cliente
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Cliente.php';

/**
 * ClienteDatos — Adaptado al schema real de la BD.
 *
 * Schema real tabla 'cliente':
 *   id_cliente, tipo_persona (NOT NULL), nombre, tipo_documento,
 *   num_documento, direccion, telefono, email
 *
 * La entidad Cliente.php usa: nombre_completo, telefono, correo, direccion
 * Mapeo:
 *   'nombre'  ↔  getNombreCompleto()
 *   'email'   ↔  getCorreo()
 *   'tipo_persona' = 'Natural' (valor fijo)
 */
class ClienteDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar nuevo cliente
    public function insertar(Cliente $cliente) {
        $tipoPersona = $cliente->getPersonalidadJuridica() ? 'Jurídica' : 'Natural';
        $sql    = "INSERT INTO cliente (tipo_persona, nombre, direccion, telefono, email, dui, nit, personalidad_juridica) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $tipoPersona,
            $cliente->getNombreCompleto(),
            $cliente->getDireccion() ?: '',
            $cliente->getTelefono(),
            $cliente->getCorreo() ?: '',
            $cliente->getDui(),
            $cliente->getNit(),
            (int)$cliente->getPersonalidadJuridica()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Modificar cliente existente
    public function modificar(Cliente $cliente) {
        $tipoPersona = $cliente->getPersonalidadJuridica() ? 'Jurídica' : 'Natural';
        $sql    = "UPDATE cliente SET tipo_persona = ?, nombre = ?, telefono = ?, email = ?, direccion = ?, dui = ?, nit = ?, personalidad_juridica = ? WHERE id_cliente = ?";
        $params = [
            $tipoPersona,
            $cliente->getNombreCompleto(),
            $cliente->getTelefono(),
            $cliente->getCorreo() ?: '',
            $cliente->getDireccion() ?: '',
            $cliente->getDui(),
            $cliente->getNit(),
            (int)$cliente->getPersonalidadJuridica(),
            $cliente->getIdCliente()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Eliminar cliente
    public function eliminar($id_cliente) {
        $sql    = "DELETE FROM cliente WHERE id_cliente = ?";
        $params = [$id_cliente];
        return $this->conexion->execute_query($sql, $params);
    }

    // Verificar si el cliente tiene ventas asociadas
    public function tieneVentasAsociadas($id_cliente) {
        $sql  = "SELECT COUNT(*) as total FROM venta WHERE id_cliente = ?";
        $fila = $this->conexion->get_record($sql, [$id_cliente]);
        return $fila && (int)$fila['total'] > 0;
    }

    // Listar todos los clientes
    public function listarTodo() {
        $sql  = "SELECT id_cliente, nombre, telefono, email, direccion, dui, nit, personalidad_juridica FROM cliente ORDER BY id_cliente DESC";
        $filas = $this->conexion->get_records($sql);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Cliente(
                $fila['id_cliente'],
                $fila['nombre'],        // → getNombreCompleto()
                $fila['telefono'] ?? '',
                $fila['email']    ?? '', // → getCorreo()
                $fila['direccion']?? '',
                $fila['dui']      ?? null,
                $fila['nit']      ?? null,
                (int)($fila['personalidad_juridica'] ?? 0)
            );
        }
        return $lista;
    }

    // Buscar cliente por ID
    public function buscarPorId($id_cliente) {
        $sql  = "SELECT id_cliente, nombre, telefono, email, direccion, dui, nit, personalidad_juridica FROM cliente WHERE id_cliente = ?";
        $fila = $this->conexion->get_record($sql, [$id_cliente]);

        if ($fila) {
            return new Cliente(
                $fila['id_cliente'],
                $fila['nombre'],
                $fila['telefono'] ?? '',
                $fila['email']    ?? '',
                $fila['direccion']?? '',
                $fila['dui']      ?? null,
                $fila['nit']      ?? null,
                (int)($fila['personalidad_juridica'] ?? 0)
            );
        }
        return null;
    }

    // Buscar clientes por nombre
    public function buscarPorNombre($busqueda) {
        $sql    = "SELECT id_cliente, nombre, telefono, email, direccion, dui, nit, personalidad_juridica FROM cliente WHERE nombre LIKE ?";
        $params = ["%" . $busqueda . "%"];
        $filas  = $this->conexion->get_records($sql, $params);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Cliente(
                $fila['id_cliente'],
                $fila['nombre'],
                $fila['telefono'] ?? '',
                $fila['email']    ?? '',
                $fila['direccion']?? '',
                $fila['dui']      ?? null,
                $fila['nit']      ?? null,
                (int)($fila['personalidad_juridica'] ?? 0)
            );
        }
        return $lista;
    }
}