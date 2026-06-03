<?php

// Incluir la clase de conexión y la entidad Venta
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Venta.php';

class VentaDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar el encabezado de una venta 
    // Retorna el ID generado por la base de datos si tiene éxito, o falso si falla.
    public function insertar(Venta $venta) {
        $sql = "INSERT INTO venta (fecha_venta, id_cliente, id_usuario, subtotal, iva, total) VALUES (NOW(), ?, ?, ?, ?, ?)";
        $params = [
            $venta->getIdCliente(),
            $venta->getIdUsuario(),
            $venta->getSubtotal(),
            $venta->getIva(),
            $venta->getTotal()
        ];
        
        $registroExitoso = $this->conexion->execute_query($sql, $params);
        
        if ($registroExitoso) {
            // Retornamos el último ID insertado para usarlo inmediatamente en los detalles
            return $this->conexion->get_last_id();
        }
        return false;
    }

    // Método para visualizar el historial completo de ventas realizadas 
    public function listarTodo() {
        $sql = "SELECT id_venta, fecha_venta, id_cliente, id_usuario, subtotal, iva, total FROM venta ORDER BY id_venta DESC";
        $filas = $this->conexion->get_records($sql);
        
        $listaVentas = [];
        foreach ($filas as $fila) {
            $listaVentas[] = new Venta(
                $fila['id_venta'],
                $fila['fecha_venta'],
                $fila['id_cliente'],
                $fila['id_usuario'],
                (double)$fila['subtotal'],
                (double)$fila['iva'],
                (double)$fila['total']
            );
        }
        return $listaVentas;
    }

    // Método para buscar una venta específica por su ID 
    public function buscarPorId($id_venta) {
        $sql = "SELECT id_venta, fecha_venta, id_cliente, id_usuario, subtotal, iva, total FROM venta WHERE id_venta = ?";
        $fila = $this->conexion->get_record($sql, [$id_venta]);
        
        if ($fila) {
            return new Venta(
                $fila['id_venta'],
                $fila['fecha_venta'],
                $fila['id_cliente'],
                $fila['id_usuario'],
                (double)$fila['subtotal'],
                (double)$fila['iva'],
                (double)$fila['total']
            );
        }
        return null;
    }


    // Reporte administrativo: Historial de ventas filtrado por rango de fechas 
    public function listarPorRangoFechas($fechaInicio, $fechaFin) {
        $sql = "SELECT id_venta, fecha_venta, id_cliente, id_usuario, subtotal, iva, total 
                FROM venta 
                WHERE DATE(fecha_venta) BETWEEN ? AND ? 
                ORDER BY fecha_venta ASC";
        $filas = $this->conexion->get_records($sql, [$fechaInicio, $fechaFin]);
        
        $listaVentas = [];
        foreach ($filas as $fila) {
            $listaVentas[] = new Venta(
                $fila['id_venta'],
                $fila['fecha_venta'],
                $fila['id_cliente'],
                $fila['id_usuario'],
                (double)$fila['subtotal'],
                (double)$fila['iva'],
                (double)$fila['total']
            );
        }
        return $listaVentas;
    }

    // Reporte administrativo: Sumatoria total acumulada de lo vendido en un periodo 
    public function obtenerMontoTotalVendido($fechaInicio, $fechaFin) {
        $sql = "SELECT SUM(total) as monto_total FROM venta WHERE DATE(fecha_venta) BETWEEN ? AND ?";
        $resultado = $this->conexion->get_record($sql, [$fechaInicio, $fechaFin]);
        
        return $resultado ? (double)$resultado['monto_total'] : 0.0;
    }
}