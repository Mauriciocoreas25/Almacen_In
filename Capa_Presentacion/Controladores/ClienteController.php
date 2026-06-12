<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit(); }

require_once __DIR__ . '/../../Capa_Negocio/ClienteNegocio.php';
require_once __DIR__ . '/../../Capa_Entidades/Cliente.php';

$accion         = $_GET['accion'] ?? '';
$clienteNegocio = new ClienteNegocio();

// ── Guardar nuevo cliente ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {

    $nombre_completo      = trim($_POST['nombre_completo']      ?? '');
    $telefono             = trim($_POST['telefono']             ?? '');
    $correo               = trim($_POST['correo']               ?? '');
    $direccion            = trim($_POST['direccion']            ?? '');
    $dui                  = trim($_POST['dui']                  ?? '');
    $nit                  = trim($_POST['nit']                  ?? '');
    $personalidad_juridica = (int)($_POST['personalidad_juridica'] ?? 0);

    if (!empty($nombre_completo) && !empty($telefono)) {
        $cliente   = new Cliente(null, $nombre_completo, $telefono, $correo, $direccion, $dui ?: null, $nit ?: null, $personalidad_juridica);
        $resultado = $clienteNegocio->registrarCliente($cliente);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Cliente '{$nombre_completo}' registrado exitosamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al guardar el cliente.";
        }
    } else {
        $_SESSION['flash_err'] = "El nombre completo y el teléfono son obligatorios.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/clientes.php');
    exit();
}

// ── Editar cliente existente ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'editar') {

    $id_cliente           = (int)($_POST['id_cliente']           ?? 0);
    $nombre_completo      = trim($_POST['nombre_completo']      ?? '');
    $telefono             = trim($_POST['telefono']             ?? '');
    $correo               = trim($_POST['correo']               ?? '');
    $direccion            = trim($_POST['direccion']            ?? '');
    $dui                  = trim($_POST['dui']                  ?? '');
    $nit                  = trim($_POST['nit']                  ?? '');
    $personalidad_juridica = (int)($_POST['personalidad_juridica'] ?? 0);

    if ($id_cliente && !empty($nombre_completo) && !empty($telefono)) {
        $cliente   = new Cliente($id_cliente, $nombre_completo, $telefono, $correo, $direccion, $dui ?: null, $nit ?: null, $personalidad_juridica);
        $resultado = $clienteNegocio->modificarCliente($cliente);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Cliente '{$nombre_completo}' actualizado correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al actualizar el cliente.";
        }
    } else {
        $_SESSION['flash_err'] = "Nombre y teléfono son obligatorios.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/clientes.php');
    exit();
}

// ── Eliminar cliente ──────────────────────────────────────
if ($accion === 'eliminar') {
    $id_cliente = (int)($_GET['id'] ?? 0);
    if ($id_cliente) {
        $resultado = $clienteNegocio->eliminarCliente($id_cliente);
        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Cliente eliminado correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al eliminar el cliente. El cliente podría tener ventas asociadas.";
        }
    }
    header('Location: /Almacen_In/Capa_Presentacion/clientes.php');
    exit();
}

header('Location: /Almacen_In/Capa_Presentacion/clientes.php');
exit();
