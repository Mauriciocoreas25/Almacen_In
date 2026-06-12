<?php

// Incluir la clase de conexión y la entidad DetalleVenta
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'DetalleVenta.php';

/**
 * DetalleVentaDatos — Adaptado al schema real de la BD.
 *
 * Schema real tabla 'detalle_venta':
 *   id_detalle_venta, id_venta, id_producto, cantidad, precio, descuento
 *
 * La entidad DetalleVenta.php usa: precio_unitario
 * Mapeo: precio ↔ precio_unitario, descuento = 0 (sin descuento)
 */
class DetalleVentaDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar un ítem de detalle de venta
    public function insertar(DetalleVenta $detalle) {
        $sql    = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio, descuento, subtotal) VALUES (?, ?, ?, ?, 0, ?)";
        $params = [
            $detalle->getIdVenta(),
            $detalle->getIdProducto(),
            $detalle->getCantidad(),
            $detalle->getPrecioUnitario(), // 'precio' en BD
            $detalle->getSubtotal()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Listar todos los ítems de una venta específica
    public function listarPorVenta($id_venta) {
        $sql  = "SELECT id_detalle_venta, id_venta, id_producto, cantidad, precio, subtotal
                 FROM detalle_venta
                 WHERE id_venta = ?";
        $filas = $this->conexion->get_records($sql, [$id_venta]);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new DetalleVenta(
                $fila['id_detalle_venta'],
                $fila['id_venta'],
                $fila['id_producto'],
                (int)$fila['cantidad'],
                (double)$fila['precio'],      // → getPrecioUnitario()
                (double)($fila['subtotal'] ?? ($fila['cantidad'] * $fila['precio']))
            );
        }
        return $lista;
    }

    // Reporte: Top productos más vendidos
    public function obtenerProductosMasVendidos($limite = 5) {
        $sql  = "SELECT id_producto, SUM(cantidad) as total_vendido
                 FROM detalle_venta
                 GROUP BY id_producto
                 ORDER BY total_vendido DESC
                 LIMIT ?";
        $filas = $this->conexion->get_records($sql, [(int)$limite]);
        return $filas;
    }
}