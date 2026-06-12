<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['id_usuario'])) { header('Location: ../login.php'); exit(); }

require_once __DIR__ . '/../../Capa_Negocio/CategoriaNegocio.php';
require_once __DIR__ . '/../../Capa_Entidades/Categoria.php';

$accion          = $_GET['accion'] ?? '';
$categoriaNegocio = new CategoriaNegocio();

// ── Guardar nueva categoría ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {

    $nombre_categoria = trim($_POST['nombre_categoria'] ?? '');
    $descripcion      = trim($_POST['descripcion']      ?? '');

    if (!empty($nombre_categoria)) {
        $nuevaCategoria = new Categoria(null, $nombre_categoria, $descripcion);
        $resultado      = $categoriaNegocio->registrarCategoria($nuevaCategoria);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Categoría '{$nombre_categoria}' creada exitosamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al guardar la categoría.";
        }
    } else {
        $_SESSION['flash_err'] = "El nombre de la categoría es obligatorio.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/categorias.php');
    exit();
}

// ── Editar categoría existente ────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'editar') {

    $id_categoria     = (int)($_POST['id_categoria']     ?? 0);
    $nombre_categoria = trim($_POST['nombre_categoria']  ?? '');
    $descripcion      = trim($_POST['descripcion']       ?? '');

    if ($id_categoria && !empty($nombre_categoria)) {
        $cat       = new Categoria($id_categoria, $nombre_categoria, $descripcion);
        $resultado = $categoriaNegocio->modificarCategoria($cat);

        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Categoría actualizada correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al actualizar la categoría.";
        }
    } else {
        $_SESSION['flash_err'] = "Datos incompletos para actualizar la categoría.";
    }

    header('Location: /Almacen_In/Capa_Presentacion/categorias.php');
    exit();
}

// ── Eliminar categoría ────────────────────────────────────
if ($accion === 'eliminar') {
    $id_categoria = (int)($_GET['id'] ?? 0);

    if ($id_categoria) {
        $resultado = $categoriaNegocio->eliminarCategoria($id_categoria);
        if ($resultado === true) {
            $_SESSION['flash_ok'] = "Categoría eliminada correctamente.";
        } else {
            $_SESSION['flash_err'] = is_string($resultado) ? $resultado : "Error al eliminar la categoría. Verifique que no tenga productos asociados.";
        }
    }

    header('Location: /Almacen_In/Capa_Presentacion/categorias.php');
    exit();
}

header('Location: /Almacen_In/Capa_Presentacion/categorias.php');
exit();
