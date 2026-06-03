<?php

// Incluir la clase de conexión y la entidad DetalleVenta
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'DetalleVenta.php';

class DetalleVentaDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar un artículo al detalle de la venta 
    public function insertar(DetalleVenta $detalle) {
        $sql = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $params = [
            $detalle->getIdVenta(),
            $detalle->getIdProducto(),
            $detalle->getCantidad(),
            $detalle->getPrecioUnitario()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar los productos de una venta específica 
    public function listarPorVenta($id_venta) {
        $sql = "SELECT id_detalle, id_venta, id_producto, cantidad, precio_unitario 
                FROM detalle_venta 
                WHERE id_venta = ?";
        $filas = $this->conexion->get_records($sql, [$id_venta]);
        
        $listaDetalles = [];
        foreach ($filas as $fila) {
            $listaDetalles = new DetalleVenta(
                $fila['id_detalle'],
                $fila['id_venta'],
                $fila['id_producto'],
                (int)$fila['cantidad'],
                (double)$fila['precio_unitario']
            );
        }
        return $listaDetalles;
    }


    // Reporte administrativo: Listado de los productos más vendidos en general o por rango 
    // Devuelve un arreglo con los IDs de productos y la cantidad total de unidades vendidas
    public function obtenerProductosMasVendidos($limite = 5) {
        $sql = "SELECT id_producto, SUM(cantidad) as total_vendido 
                FROM detalle_venta 
                GROUP BY id_producto 
                ORDER BY total_vendido DESC 
                LIMIT ?";
        
        // Ejecutamos pasando el límite de filas deseado para el "Top"
        $filas = $this->conexion->get_records($sql, [(int)$limite]);
        return $filas; 
    }
}