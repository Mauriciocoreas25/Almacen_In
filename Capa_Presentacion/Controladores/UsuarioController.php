<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Seguridad: Solo usuarios logueados pueden usar este controlador
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../login.php');
    exit();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Negocio' . DIRECTORY_SEPARATOR . 'UsuarioNegocio.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Usuario.php';

$accion = $_GET['accion'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'guardar') {
    
    // Capturar datos del formulario flotante
    $nombre_complete = isset($_POST['nombre_complete']) ? trim($_POST['nombre_complete']) : '';
    $username        = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password        = isset($_POST['password']) ? trim($_POST['password']) : '';
    $id_rol          = isset($_POST['id_rol']) ? (int)$_POST['id_rol'] : null;

    if (!empty($nombre_complete) && !empty($username) && !empty($password) && $id_rol) {
        
        // Instanciar la entidad respetando el orden exacto de tu constructor:
        // 1.id_usuario, 2.username, 3.password, 4.nombre_complete, 5.id_rol, 6.estado
        $nuevoUsuario = new Usuario(null, $username, $password, $nombre_complete, $id_rol, true);
        
        $usuarioNegocio = new UsuarioNegocio();
        $resultado = $usuarioNegocio->registrarUsuario($nuevoUsuario);
        
        if ($resultado) {
            // Regresar directo al dashboard para ver el nuevo usuario insertado
            header('Location: /Almacen_In/Capa_Presentacion/dashboard.php');
            exit();
        } else {
            die("Error: No se pudo guardar el usuario en la base de datos.");
        }
    } else {
        die("Error: Faltan campos obligatorios.");
    }
} else {
    header('Location: /Almacen_In/Capa_Presentacion/dashboard.php');
    exit();
}