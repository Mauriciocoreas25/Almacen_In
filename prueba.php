<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Usando el nombre exacto de tu carpeta: Capa_Datos
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'Conexion.php';

echo "<h2>--- Test de Arquitectura Multicapa (Almacen_In) ---</h2>";

// 1. Intentar conectar a MySQL utilizando tu clase Conexion
echo "<b>Intentando conectar a MySQL (Laragon) mediante Capa_Datos...</b><br>";

$conexion = new Conexion();
$db = $conexion->conectar();

if ($db) {
    echo "<br><span style='color: green; font-weight: bold; font-size: 1.2em;'>✔ ¡Éxito rotundo! Conexión PDO establecida con la base de datos local.</span><br>";
    
    // Cerrar de forma limpia
    $conexion->cerrar_conexion();
    echo "✔ Conexión cerrada de forma segura para liberar recursos.<br>";
}