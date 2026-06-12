<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit(); }

require_once __DIR__ . '/../../Capa_Negocio/ProductoNegocio.php';
require_once __DIR__ . '/../../Capa_Entidades/Producto.php';
require_once __DIR__ . '/../../Capa_Datos/ProductoDatos.php';

$accion          = $_GET['accion'] ?? '';
$productoNegocio = new ProductoNegocio();

// ── Guardar nuevo producto ────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {

    $codigo       = trim($_POST['codigo']       ?? '');
    $nombre       = trim($_POST['nombre']       ?? '');
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $precio       = (float)($_POST['precio']    ?? 0);
    $stock        = (int)($_POST['stock']        ?? 0);

    if (!empty($codigo) && !empty($nombre) && $id_categoria) {
        $prod      = new Producto(null, $codigo, $nombre, $id_categoria, $precio, $stock, true);
        $resultado = $productoNegocio->registrarProducto($prod);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Producto '{$nombre}' registrado exitosamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al guardar el producto.";
        }
    } else {
        $_SESSION['flash_err'] = "Código, nombre y categoría son obligatorios.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/productos.php');
    exit();
}

// ── Editar producto existente ─────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'editar') {

    $id_producto  = (int)($_POST['id_producto']  ?? 0);
    $codigo       = trim($_POST['codigo']        ?? '');
    $nombre       = trim($_POST['nombre']        ?? '');
    $id_categoria = (int)($_POST['id_categoria'] ?? 0);
    $precio       = (float)($_POST['precio']     ?? 0);
    $stock        = (int)($_POST['stock']        ?? 0);

    if ($id_producto && !empty($codigo) && !empty($nombre) && $id_categoria) {
        $prod      = new Producto($id_producto, $codigo, $nombre, $id_categoria, $precio, $stock, true);
        $resultado = $productoNegocio->modificarProducto($prod);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Producto actualizado correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al actualizar el producto.";
        }
    } else {
        $_SESSION['flash_err'] = "Datos incompletos para actualizar el producto.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/productos.php');
    exit();
}

// ── Cambiar estado (activar / desactivar) ─────────────────
if ($accion === 'cambiar_estado') {
    $id_producto = (int)($_GET['id'] ?? 0);
    if ($id_producto) {
        // Obtener estado actual y alternarlo directamente en la BD
        $productoDatos = new ProductoDatos();
        $prod = $productoDatos->buscarPorId($id_producto);
        if ($prod) {
            $nuevoEstado = !$prod->getEstado();
            $prod->setEstado($nuevoEstado);
            $resultado = $productoDatos->modificar($prod);
            $_SESSION[$resultado ? 'flash_ok' : 'flash_err'] = $resultado
                ? ($nuevoEstado ? "Producto activado." : "Producto desactivado.")
                : "Error al cambiar estado del producto.";
        }
    }
    header('Location: /Almacen_In/Capa_Presentacion/productos.php');
    exit();
}

// ── Eliminar producto ─────────────────────────────────────
if ($accion === 'eliminar') {
    $id_producto = (int)($_GET['id'] ?? 0);
    if ($id_producto) {
        $resultado = $productoNegocio->eliminarProducto($id_producto);
        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Producto eliminado correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al eliminar el producto.";
        }
    }
    header('Location: /Almacen_In/Capa_Presentacion/productos.php');
    exit();
}

header('Location: /Almacen_In/Capa_Presentacion/productos.php');
exit();
