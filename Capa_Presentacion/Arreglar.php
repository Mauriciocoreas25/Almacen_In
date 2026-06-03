<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Cargar la conexión directamente
require_once __DIR__ . '/../Capa_Datos/Conexion.php';

$conexion = new Conexion();

echo "<h1>Generando Usuario para la Defensa...</h1>";

try {
    // 1. Apagar candados de seguridad
    $conexion->execute_query("SET FOREIGN_KEY_CHECKS = 0;");
    
    // CORREGIDO: Usamos DELETE para burlar la restricción de la tabla venta
    $conexion->execute_query("DELETE FROM usuario;");
    
    // Reiniciar el contador del AUTO_INCREMENT para que inicie en 1
    $conexion->execute_query("ALTER TABLE usuario AUTO_INCREMENT = 1;");
    
    // Volver a encender los candados
    $conexion->execute_query("SET FOREIGN_KEY_CHECKS = 1;");
    echo "-> Tabla limpiada con éxito usando DELETE.<br>";

    // 2. GENERAR EL HASH LEGÍTIMO DESDE TU PROPIO PHP
    $passwordLimpia = 'admin123';
    $hashReal = password_hash($passwordLimpia, PASSWORD_BCRYPT);

    echo "-> Hash real generado por tu servidor: <code>" . $hashReal . "</code><br>";

    // 3. Meter el usuario Mauricio
    $sql = "INSERT INTO usuario (id_rol, usuario, nombre, apellidos, tipo_documento, num_documento, direccion, telefono, email, clave, estado) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, b'1')";

    $params = [
        1, 
        'Mauricio',      
        'Mauricio', 
        'Coreas', 
        'DUI', 
        '04752563-5', 
        'San Miguel', 
        '7007-9140', 
        'admin@correo.com', 
        $hashReal        
    ];

    $resultado = $conexion->execute_query($sql, $params);

    if ($resultado) {
        echo "<h2>¡USUARIO CREADO CON ÉXITO!</h2>";
        echo "Ya podés ir al login e ingresar con estos datos exactos:<br>";
        echo "<strong>Usuario:</strong> Mauricio<br>";
        echo "<strong>Contraseña:</strong> admin123";
    } else {
        echo "<h2 style='color:red;'>Falló la inserción en la base de datos.</h2>";
    }

} catch (Exception $e) {
    echo "<h2 style='color:red;'>Error en el script:</h2> " . $e->getMessage();
}