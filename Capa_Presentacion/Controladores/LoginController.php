<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Negocio' . DIRECTORY_SEPARATOR . 'UsuarioNegocio.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $username = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $usuarioNegocio = new UsuarioNegocio();
    $resultado = $usuarioNegocio->iniciarSesion($username, $password);

    if ($resultado === true) {
        header('Location: /Almacen_In/Capa_Presentacion/dashboard.php');
        exit();
    } else {
        $_SESSION['error_login'] = $resultado;
        header('Location: /Almacen_In/Capa_Presentacion/login.php');
        exit();
    }
} else {
    header('Location: /Almacen_In/Capa_Presentacion/login.php');
    exit();
}