<?php
/**
 * includes/header.php — Componente HEAD reutilizable
 * Uso: require_once __DIR__ . '/includes/header.php';
 * Variable esperada: $pageTitle (string) — Título de la pestaña
 */
$pageTitle = $pageTitle ?? 'Sistema de Control de Inventario & Ventas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Control de Inventario &amp; Ventas">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="/Almacen_In/Capa_Presentacion/assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">

    <!-- Generic Bootstrap Layout Helper -->
    <link rel="stylesheet" href="/Almacen_In/Capa_Presentacion/assets/bootstrap-5.3.8-dist/css/bootstrap-layout.css">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Favicon inline SVG -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏪</text></svg>">
</head>
