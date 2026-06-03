<?php
// Ruta absoluta hacia el archivo .env en la raíz del proyecto
$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env'; // __DIR__: Directorio actual del archivo env.php


if (!file_exists($path)) {
    die("Error crítico: El archivo .env no existe en la raíz del proyecto.");
}

// Leer las líneas del archivo .env omitiendo líneas vacías
$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    // Ignorar líneas que sean comentarios
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    // Dividir la línea en clave y valor mediante el primer "=" que encuentre
    if (strpos($line, '=') !== false) { 
        list($key, $value) = explode('=', $line, 2);
        
        $key = trim($key);
        $value = trim($value);

        // Cargar la variable en los arreglos globales de PHP
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}