<?php

// Incluir de forma obligatoria el cargador de variables de entorno
require_once __DIR__ . '/../config/env.php';

class Conexion {
    // Atributos privados  extraídos del archivo .env
    private $server;
    private $db_name;
    private $user;
    private $password;
    private $charset;
    
    // Atributos de control de la extensión PDO
    // PDO (PHP Data Objects): Es un adaptador universal de bases de datos para PHP.
// Actúa como un traductor: a PHP le da igual si usamos MySQL, PostgreSQL o SQLite, 
// PDO estandariza el código, maneja la conexión y nos protege de ataques SQL Injection.
    public $pdo;
    private $error;

    // Constructor: Inicializa los atributos con los datos guardados en $_ENV, con fallbacks seguros
    public function __construct() {
        $this->server   = $_ENV['DB_SERVER']   ?? 'localhost';
        $this->db_name  = $_ENV['DB_NAME']     ?? 'almacen_inventario';
        $this->user     = $_ENV['DB_USER']     ?? 'root';
        $this->password = $_ENV['DB_PASS']     ?? '';
        $this->charset  = $_ENV['DB_CHARSET']  ?? 'utf8mb4';
    }

    // Método para establecer la conexión con la base de datos utilizando PDO
    public function conectar() {
        // Configurar la cadena de conexión (Data Source Name)
        $dsn = "mysql:host={$this->server};dbname={$this->db_name};charset={$this->charset}";
        
        // Opciones de configuración de PDO para manejo seguro de datos
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa el manejo de excepciones (errores)
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos como arreglos asociativos
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad
        ];

        try {
            // Crear la instancia de PDO
            $this->pdo = new PDO($dsn, $this->user, $this->password, $options);
            return $this->pdo;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            die("Error: No fue posible establecer una conexión con la base de datos. " . $this->error);
        }
    }

    // Método para cerrar la conexión de la base de datos y liberar recursos
    public function cerrar_conexion() {
        $this->pdo = null;
    }

    // Método para ejecutar consultas de manipulación de datos (INSERT, UPDATE, DELETE)
    public function execute_query($sql, $params = []) {
        try {
            $this->conectar();
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            $this->cerrar_conexion();
            return $result; // Retorna true si fue exitosa, false si no
        } catch (PDOException $e) {
            $this->cerrar_conexion();
            die("Error al ejecutar la consulta: " . $e->getMessage());
        }
    }

    // Método para INSERT que retorna el ID generado automáticamente
    public function execute_insert($sql, $params = []) {
        try {
            $this->conectar();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $lastId = $this->pdo->lastInsertId(); // Se obtiene ANTES de cerrar
            $this->cerrar_conexion();
            return $lastId ? (int)$lastId : false;
        } catch (PDOException $e) {
            $this->cerrar_conexion();
            die("Error al ejecutar la consulta: " . $e->getMessage());
        }
    }

    // Método para retornar múltiples registros (Consultas tipo SELECT)
    public function get_records($sql, $params = []) {
        try {
            $this->conectar();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $resultados = $stmt->fetchAll();
            $this->cerrar_conexion();
            return $resultados; // Devuelve un array con todas las filas encontradas
        } catch (PDOException $e) {
            $this->cerrar_conexion();
            die("Error al obtener los registros: " . $e->getMessage());
        }
    }

    // Método para retornar únicamente un registro (Consultas tipo SELECT con una sola fila)
    public function get_record($sql, $params = []) {
        try {
            $this->conectar();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch();
            $this->cerrar_conexion();
            return $resultado ? $resultado : null; // Devuelve la fila o null si no hay datos
        } catch (PDOException $e) {
            $this->cerrar_conexion();
            die("Error al obtener el registro: " . $e->getMessage());
        }
    }
}