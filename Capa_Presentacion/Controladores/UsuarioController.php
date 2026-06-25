<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once __DIR__ . '/../../Capa_Negocio/UsuarioNegocio.php';
require_once __DIR__ . '/../../Capa_Entidades/Usuario.php';

// Acción de logout (GET)
$accion = $_GET['accion'] ?? ($_POST['accion'] ?? '');

if ($accion === 'logout') {
    session_destroy();
    header('Location: /Almacen_In/Capa_Presentacion/login.php');
    exit();
}

// Seguridad: Solo usuarios logueados pueden usar este controlador
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit();
}
if ($_SESSION['id_rol'] != 1) {
    header('Location: ../dashboard.php');
    exit();
}

$usuarioNegocio = new UsuarioNegocio();

// ── Guardar nuevo usuario ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {

    $nombre_complete = trim($_POST['nombre_complete'] ?? '');
    $username        = trim($_POST['username']        ?? '');
    $password        = trim($_POST['password']        ?? '');
    $id_rol          = (int)($_POST['id_rol']         ?? 0);

    if (!empty($nombre_complete) && !empty($username) && !empty($password) && $id_rol) {
        $nuevoUsuario = new Usuario(null, $username, $password, $nombre_complete, $id_rol, true);
        $resultado    = $usuarioNegocio->registrarUsuario($nuevoUsuario);

        if ($resultado) {
            $_SESSION['flash_ok'] = "Usuario '{$username}' registrado exitosamente.";
        } else {
            $_SESSION['flash_err'] = "Error: No se pudo guardar el usuario. El nombre de usuario podría ya existir.";
        }
    } else {
        $_SESSION['flash_err'] = "Error: Todos los campos son obligatorios.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/usuarios.php');
    exit();
}

// ── Editar usuario existente ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'editar') {

    $id_usuario      = (int)($_POST['id_usuario']      ?? 0);
    $nombre_complete = trim($_POST['nombre_complete']  ?? '');
    $username        = trim($_POST['username']         ?? '');
    $password        = trim($_POST['password']         ?? ''); // vacío = sin cambio
    $id_rol          = (int)($_POST['id_rol']          ?? 0);

    if ($id_usuario && !empty($nombre_complete) && !empty($username) && $id_rol) {
        // Construir entidad: si password vacío modificarUsuario() conserva el hash actual
        $usuarioEditado = new Usuario($id_usuario, $username, $password, $nombre_complete, $id_rol, true);
        $resultado      = $usuarioNegocio->modificarUsuario($usuarioEditado);

        if ($resultado) {
            $_SESSION['flash_ok'] = "Usuario '{$username}' actualizado correctamente.";
        } else {
            $_SESSION['flash_err'] = "Error al actualizar el usuario.";
        }
    } else {
        $_SESSION['flash_err'] = "Datos incompletos para actualizar el usuario.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/usuarios.php');
    exit();
}

// ── Cambiar estado (activar / desactivar) ─────────────────
if ($accion === 'cambiar_estado') {
    $id_usuario = (int)($_GET['id'] ?? 0);

    if ($id_usuario) {
        // Obtener estado actual del usuario y alternarlo
        $usuarioActual = $usuarioNegocio->obtenerPorId($id_usuario);
        if ($usuarioActual) {
            $nuevoEstado = !$usuarioActual->getEstado();
            $resultado   = $usuarioNegocio->cambiarEstadoUsuario($id_usuario, $nuevoEstado);
            if ($resultado) {
                $_SESSION['flash_ok'] = $nuevoEstado ? "Usuario activado." : "Usuario desactivado.";
            } else {
                $_SESSION['flash_err'] = "Error al cambiar el estado del usuario.";
            }
        }
    }

    header('Location: /Almacen_In/Capa_Presentacion/usuarios.php');
    exit();
}

// Fallback
header('Location: /Almacen_In/Capa_Presentacion/usuarios.php');
exit();