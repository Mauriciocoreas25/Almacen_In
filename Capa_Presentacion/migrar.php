<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Capa_Datos/Conexion.php';

$conexion = new Conexion();

echo "<h1>Ejecutando Migración de Base de Datos...</h1>";

try {
    // 1. Agregar columnas a la tabla 'cliente'
    $columns = $conexion->get_records("SHOW COLUMNS FROM cliente");
    $columnNames = array_column($columns, 'Field');

    if (!in_array('dui', $columnNames)) {
        $conexion->execute_query("ALTER TABLE cliente ADD COLUMN dui VARCHAR(20) DEFAULT NULL;");
        echo "<p style='color:green;'>✔ Columna 'dui' agregada a la tabla 'cliente'.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ La columna 'dui' ya existe en 'cliente'.</p>";
    }

    if (!in_array('nit', $columnNames)) {
        $conexion->execute_query("ALTER TABLE cliente ADD COLUMN nit VARCHAR(20) DEFAULT NULL;");
        echo "<p style='color:green;'>✔ Columna 'nit' agregada a la tabla 'cliente'.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ La columna 'nit' ya existe en 'cliente'.</p>";
    }

    if (!in_array('personalidad_juridica', $columnNames)) {
        $conexion->execute_query("ALTER TABLE cliente ADD COLUMN personalidad_juridica TINYINT(1) NOT NULL DEFAULT 0;");
        echo "<p style='color:green;'>✔ Columna 'personalidad_juridica' agregada a la tabla 'cliente'.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ La columna 'personalidad_juridica' ya existe en 'cliente'.</p>";
    }

    // 2. Agregar columna 'subtotal' a la tabla 'detalle_venta'
    $columnsDetails = $conexion->get_records("SHOW COLUMNS FROM detalle_venta");
    $detailsColumnNames = array_column($columnsDetails, 'Field');

    if (!in_array('subtotal', $detailsColumnNames)) {
        $conexion->execute_query("ALTER TABLE detalle_venta ADD COLUMN subtotal DECIMAL(11,2) NOT NULL DEFAULT 0.00;");
        echo "<p style='color:green;'>✔ Columna 'subtotal' agregada a la tabla 'detalle_venta'.</p>";
        
        // Calcular subtotales existentes
        $conexion->execute_query("UPDATE detalle_venta SET subtotal = cantidad * precio;");
        echo "<p style='color:green;'>✔ Subtotales recalculados para los registros existentes de detalle_venta.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ La columna 'subtotal' ya existe en 'detalle_venta'.</p>";
    }

    // 3. Convertir columnas BIT(1) a TINYINT(1) y expandir num_comprobante
    $conexion->execute_query("ALTER TABLE producto MODIFY COLUMN estado TINYINT(1) NOT NULL DEFAULT 1;");
    $conexion->execute_query("ALTER TABLE categoria MODIFY COLUMN estado TINYINT(1) NOT NULL DEFAULT 1;");
    $conexion->execute_query("ALTER TABLE usuario MODIFY COLUMN estado TINYINT(1) NOT NULL DEFAULT 1;");
    $conexion->execute_query("ALTER TABLE rol MODIFY COLUMN estado TINYINT(1) NOT NULL DEFAULT 1;");
    echo "<p style='color:green;'>✔ Columnas de 'estado' convertidas a TINYINT(1) en las tablas producto, categoria, usuario y rol.</p>";

    $conexion->execute_query("ALTER TABLE venta MODIFY COLUMN num_comprobante VARCHAR(20) NOT NULL;");
    echo "<p style='color:green;'>✔ Columna 'num_comprobante' de la tabla 'venta' expandida a VARCHAR(20).</p>";

    // 4. Asegurar al menos 3 roles de usuario
    $roles = $conexion->get_records("SELECT id_rol FROM rol");
    $rolIds = array_column($roles, 'id_rol');
    if (!in_array(3, $rolIds)) {
        $conexion->execute_query("INSERT INTO rol (id_rol, nombre, descripcion, estado) VALUES (3, 'Supervisor', 'Supervisor de almacén y inventarios', 1);");
        echo "<p style='color:green;'>✔ Rol 'Supervisor' (ID 3) insertado correctamente para cumplir con el requerimiento de al menos 3 niveles de usuario.</p>";
    } else {
        echo "<p style='color:orange;'>ℹ El rol 'Supervisor' (ID 3) ya existe.</p>";
    }

    echo "<h2>¡Migración completada con éxito!</h2>";
    echo "<p>Ya puedes eliminar este archivo <code>migrar.php</code> de la carpeta Capa_Presentacion por seguridad.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>Error durante la migración:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
