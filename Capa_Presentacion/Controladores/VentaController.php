<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit(); }

$accion = $_GET['accion'] ?? '';

// ── Procesar venta completa ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'procesar') {

    require_once __DIR__ . '/../../Capa_Negocio/VentaNegocio.php';

    $id_usuario = (int)$_SESSION['id_usuario'];
    $id_cliente = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;
    $carritoJson = $_POST['carrito_json'] ?? '[]';

    // Decodificar el carrito enviado en JSON desde el frontend
    $carritoItems = json_decode($carritoJson, true);

    if (empty($carritoItems) || !is_array($carritoItems)) {
        $_SESSION['flash_err'] = "Error: El carrito está vacío o tiene un formato inválido.";
        header('Location: /Almacen_In/Capa_Presentacion/ventas.php');
        exit();
    }

    // Construir el array que espera procesarVenta()
    $carritoParaNegocio = [];
    foreach ($carritoItems as $item) {
        $carritoParaNegocio[] = [
            'id_producto'      => (int)$item['id'],
            'cantidad'         => (int)$item['cantidad'],
            'precio_unitario'  => (float)$item['precio'],
        ];
    }

    $ventaNegocio = new VentaNegocio();
    $resultado    = $ventaNegocio->procesarVenta($id_cliente, $id_usuario, $carritoParaNegocio);

    if (is_numeric($resultado)) {
        $idVenta = (int)$resultado;

        $_SESSION['flash_ok'] = "✅ Venta procesada exitosamente.";
        header('Location: /Almacen_In/Capa_Presentacion/comprobante.php?id=' . $idVenta);
        exit();
    } else {
        // Error con mensaje descriptivo (ej. stock insuficiente)
        $msgError = is_string($resultado) ? $resultado : "Error desconocido al procesar la venta.";
        $_SESSION['flash_err'] = $msgError;
        header('Location: /Almacen_In/Capa_Presentacion/ventas.php');
        exit();
    }
}

// Fallback
header('Location: /Almacen_In/Capa_Presentacion/ventas.php');
exit();
