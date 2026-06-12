<?php

// Incluir la clase de conexión y la entidad Venta
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Venta.php';

/**
 * VentaDatos — Adaptado al schema real de la BD.
 *
 * Schema real tabla 'venta':
 *   id_venta, id_cliente, id_usuario, tipo_comprobante,
 *   serie_comprobante, num_comprobante, fecha_hora,
 *   impuesto (decimal 4,2 = tasa IVA), total, estado
 *
 * NOTA: La entidad Venta.php maneja: id_venta, fecha_venta,
 * id_cliente, id_usuario, subtotal, iva, total.
 * Se hace el mapeo:
 *   - 'fecha_hora'  ↔  'fecha_venta'
 *   - 'impuesto'    →  se almacena la tasa (0.13); subtotal = total / 1.13 (calculado)
 *   - 'total'       ↔  'total'
 *   - 'tipo_comprobante' = 'BOLETA'  (valor fijo)
 *   - 'serie_comprobante' = 'A001'   (valor fijo)
 *   - 'num_comprobante'   = secuencial automático
 *   - 'estado'      = 'Registrada'  (valor fijo)
 *   - 'id_cliente'  = 0 si viene NULL (consumidor final)
 */
class VentaDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar encabezado de la venta. Retorna el ID generado.
    public function insertar(Venta $venta) {
        // id_cliente es NOT NULL con FK: si viene null usamos ID 1 = "Consumidor Final"
        $idCliente = $venta->getIdCliente() ? (int)$venta->getIdCliente() : 1;

        // Número de comprobante automático con timestamp
        $numComprobante = date('YmdHis');

        // Calcular el subtotal desde el total (total incluye IVA del 13%)
        $total    = (double)$venta->getTotal();
        $subtotal = $venta->getSubtotal() > 0 ? $venta->getSubtotal() : round($total / 1.13, 2);

        $sql = "INSERT INTO venta
                    (id_cliente, id_usuario, tipo_comprobante, serie_comprobante, num_comprobante, fecha_hora, impuesto, total, estado)
                VALUES
                    (?, ?, 'BOLETA', 'A001', ?, NOW(), 0.13, ?, 'Registrada')";

        $params = [
            $idCliente,
            (int)$venta->getIdUsuario(),
            $numComprobante,
            $total
        ];

        $ok = $this->conexion->execute_insert($sql, $params);
        return $ok; // retorna el ID insertado, o false si falló
    }

    // Listar historial completo
    public function listarTodo() {
        $sql = "SELECT id_venta, fecha_hora, id_cliente, id_usuario, impuesto, total
                FROM venta
                ORDER BY id_venta DESC";
        $filas = $this->conexion->get_records($sql);

        $lista = [];
        foreach ($filas as $fila) {
            $total    = (double)$fila['total'];
            $tasa     = (double)$fila['impuesto']; // 0.13
            // Reconstruir subtotal e iva a partir del total + tasa almacenada
            $subtotal = round($total / (1 + $tasa), 2);
            $iva      = round($total - $subtotal, 2);

            $lista[] = new Venta(
                $fila['id_venta'],
                $fila['fecha_hora'],    // → getFechaVenta()
                $fila['id_cliente'],
                $fila['id_usuario'],
                $subtotal,
                $iva,
                $total
            );
        }
        return $lista;
    }

    // Buscar venta por ID
    public function buscarPorId($id_venta) {
        $sql  = "SELECT id_venta, fecha_hora, id_cliente, id_usuario, impuesto, total
                 FROM venta WHERE id_venta = ?";
        $fila = $this->conexion->get_record($sql, [$id_venta]);

        if ($fila) {
            $total    = (double)$fila['total'];
            $tasa     = (double)$fila['impuesto'];
            $subtotal = round($total / (1 + $tasa), 2);
            $iva      = round($total - $subtotal, 2);

            return new Venta(
                $fila['id_venta'],
                $fila['fecha_hora'],
                $fila['id_cliente'],
                $fila['id_usuario'],
                $subtotal,
                $iva,
                $total
            );
        }
        return null;
    }

    // Reporte: ventas por rango de fechas
    public function listarPorRangoFechas($fechaInicio, $fechaFin) {
        $sql  = "SELECT id_venta, fecha_hora, id_cliente, id_usuario, impuesto, total
                 FROM venta
                 WHERE DATE(fecha_hora) BETWEEN ? AND ?
                 ORDER BY fecha_hora ASC";
        $filas = $this->conexion->get_records($sql, [$fechaInicio, $fechaFin]);

        $lista = [];
        foreach ($filas as $fila) {
            $total    = (double)$fila['total'];
            $tasa     = (double)$fila['impuesto'];
            $subtotal = round($total / (1 + $tasa), 2);
            $iva      = round($total - $subtotal, 2);

            $lista[] = new Venta(
                $fila['id_venta'],
                $fila['fecha_hora'],
                $fila['id_cliente'],
                $fila['id_usuario'],
                $subtotal,
                $iva,
                $total
            );
        }
        return $lista;
    }

    // Reporte: monto total acumulado en un período
    public function obtenerMontoTotalVendido($fechaInicio, $fechaFin) {
        $sql       = "SELECT SUM(total) as monto_total FROM venta WHERE DATE(fecha_hora) BETWEEN ? AND ?";
        $resultado = $this->conexion->get_record($sql, [$fechaInicio, $fechaFin]);
        return $resultado ? (double)$resultado['monto_total'] : 0.0;
    }
}