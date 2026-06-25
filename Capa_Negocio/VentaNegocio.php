<?php

// Incluir las capas de datos necesarias
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'VentaDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'DetalleVentaDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'ProductoDatos.php';

// Incluir las entidades correspondientes
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Venta.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'DetalleVenta.php';

class VentaNegocio {
    private $ventaDatos;
    private $detalleVentaDatos;
    private $productoDatos;

    public function __construct() {
        $this->ventaDatos = new VentaDatos();
        $this->detalleVentaDatos = new DetalleVentaDatos();
        $this->productoDatos = new ProductoDatos();
    }

    // --- PROCESO MAESTRO: REGISTRAR VENTA COMPLETA 
    // Recibe el ID del cliente, el ID del usuario logueado y un array con los productos elegidos
    public function procesarVenta($id_cliente, $id_usuario, $carritoProductos) {
        if (empty($carritoProductos)) {
            return "No se puede procesar una venta sin productos en el carrito.";
        }

        // 1. VALIDACIÓN PREVIA DE STOCK 
        foreach ($carritoProductos as $item) {
            $producto = $this->productoDatos->buscarPorId($item['id_producto']);
            if (!$producto) {
                return "Uno de los productos seleccionados no existe.";
            }
            if ($producto->getStock() < $item['cantidad']) {
                return "Stock insuficiente para el producto: " . $producto->getNombre() . ". Disponible: " . $producto->getStock();
            }
        }
        // 2. CÁLCULO AUTOMÁTICO DE MONTOS (IVA Incluido)
        $total = 0;
        foreach ($carritoProductos as $item) {
            $total += ($item['precio_unitario'] * $item['cantidad']);
        }
        
        $tasaIva = 0.13;
        $subtotal = round($total / (1 + $tasaIva), 2);
        $iva = round($total - $subtotal, 2);

        // 3. REGISTRAR ENCABEZADO DE LA VENTA
        $nuevaVenta = new Venta(null, null, $id_cliente, $id_usuario, $subtotal, $iva, $total);
        $idVentaGenerado = $this->ventaDatos->insertar($nuevaVenta);

        if (!$idVentaGenerado) {
            return "Error al intentar registrar el encabezado de la venta.";
        }

        // 4. REGISTRAR DETALLES Y ACTUALIZAR EL STOCK AUTOMÁTICAMENTE 
        foreach ($carritoProductos as $item) {
            // Guardar fila del detalle
            $detalle = new DetalleVenta(null, $idVentaGenerado, $item['id_producto'], $item['cantidad'], $item['precio_unitario']);
            $this->detalleVentaDatos->insertar($detalle);

            // Restar la cantidad vendida al stock del producto (Se pasa en negativo para restar)
            $cantidadDescontar = -$item['cantidad'];
            $this->productoDatos->actualizarStock($item['id_producto'], $cantidadDescontar);
        }

        return $idVentaGenerado; // Transacción completada con éxito, retorna el ID de la venta
    }

    // Listar historial de transacciones 
    public function listarHistorialVentas() {
        return $this->ventaDatos->listarTodo();
    }

    // Obtener los datos completos de una venta por su ID (Para generación de comprobantes RF-13)
    public function obtenerVentaPorId($id_venta) {
        return $this->ventaDatos->buscarPorId($id_venta);
    }

    // --- MÉTODOS EXCLUSIVOS PARA REPORTES ADMINISTRATIVOS 
    // Filtrar ventas por rango de fechas
    public function generarReporteVentasPorFechas($fechaInicio, $fechaFin) {
        if (empty($fechaInicio) || empty($fechaFin)) {
            return [];
        }
        return $this->ventaDatos->listarPorRangoFechas($fechaInicio, $fechaFin);
    }

    // Obtener ingresos totales acumulados en un período
    public function obtenerIngresosTotales($fechaInicio, $fechaFin) {
        if (empty($fechaInicio) || empty($fechaFin)) {
            return 0.0;
        }
        return $this->ventaDatos->obtenerMontoTotalVendido($fechaInicio, $fechaFin);
    }

    // Obtener el listado de productos más vendidos ordenados por cantidad
    public function generarReporteProductosMasVendidos($limite = 5) {
        $topProductos = $this->detalleVentaDatos->obtenerProductosMasVendidos($limite);
        
        $reporteCompleto = [];
        foreach ($topProductos as $fila) {
            $producto = $this->productoDatos->buscarPorId($fila['id_producto']);
            if ($producto) {
                $reporteCompleto[] = [
                    'codigo' => $producto->getCodigo(),
                    'nombre' => $producto->getNombre(),
                    'total_unidades_vendidas' => $fila['total_vendido']
                ];
            }
        }
        return $reporteCompleto;
    }

    // Anular una venta, devolviendo el stock de los productos vendidos
    public function anularVenta($id_venta) {
        if (empty($id_venta)) {
            return "ID de venta inválido.";
        }

        // 1. Obtener la venta
        $venta = $this->ventaDatos->buscarPorId($id_venta);
        if (!$venta) {
            return "La venta especificada no existe.";
        }

        // Verificar que no esté ya anulada
        if ($venta->getEstado() === 'Anulada') {
            return "Esta venta ya se encuentra anulada.";
        }

        // 2. Obtener los detalles de la venta
        $detalles = $this->detalleVentaDatos->listarPorVenta($id_venta);
        if (empty($detalles)) {
            return "No se encontraron detalles para esta venta.";
        }

        // 3. Devolver los productos al inventario
        foreach ($detalles as $det) {
            // Actualizar stock sumando la cantidad original de la venta
            $this->productoDatos->actualizarStock($det->getIdProducto(), $det->getCantidad());
        }

        // 4. Actualizar el estado de la venta en base de datos
        $resultado = $this->ventaDatos->anular($id_venta);
        if ($resultado) {
            return true;
        }

        return "Error al intentar anular la venta en la base de datos.";
    }
}