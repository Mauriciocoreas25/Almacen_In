<?php

// Incluir la clase de conexión y la entidad Producto
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Producto.php';

class ProductoDatos {
    private $conexion;

    // Constructor: Inicializa el objeto de conexión
    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Método para insertar un nuevo producto 
    public function insertar(Producto $producto) {
        $sql = "INSERT INTO producto (codigo, nombre, id_categoria, precio, stock, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getIdCategoria(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getEstado() ? 1 : 0
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para modificar un producto existente 
    public function modificar(Producto $producto) {
        $sql = "UPDATE producto SET codigo = ?, nombre = ?, id_categoria = ?, precio = ?, stock = ?, estado = ? WHERE id_producto = ?";
        $params = [
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getIdCategoria(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getEstado() ? 1 : 0,
            $producto->getIdProducto()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para eliminar un producto 
    public function eliminar($id_producto) {
        $sql = "DELETE FROM producto WHERE id_producto = ?";
        $params = [$id_producto];
        return $this->conexion->execute_query($sql, $params);
    }

    // Método para listar todos los productos del inventario
    public function listarTodo() {
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, precio, stock, estado FROM producto";
        $filas = $this->conexion->get_records($sql);
        
        $listaProductos = [];
        foreach ($filas as $fila) {
            $listaProductos[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $listaProductos;
    }

    // Método para buscar un producto específico por su ID
    public function buscarPorId($id_producto) {
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, precio, stock, estado FROM producto WHERE id_producto = ?";
        $fila = $this->conexion->get_record($sql, [$id_producto]);
        
        if ($fila) {
            return new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return null;
    }

    // Método para buscar productos mediante filtros de texto 
    public function buscarPorFiltro($busqueda) {
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, precio, stock, estado 
                FROM producto 
                WHERE nombre LIKE ? OR codigo LIKE ?";
        $termino = "%" . $busqueda . "%";
        $filas = $this->conexion->get_records($sql, [$termino, $termino]);
        
        $listaProductos = [];
        foreach ($filas as $fila) {
            $listaProductos[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $listaProductos;
    }

    // Método operativo para actualizar stock en compras/ventas 
    public function actualizarStock($id_producto, $cantidad) {
        $sql = "UPDATE producto SET stock = stock + (?) WHERE id_producto = ?";
        $params = [(int)$cantidad, $id_producto];
        return $this->conexion->execute_query($sql, $params);
    }

  

    // Reporte administrativo: Productos con bajo stock 
    public function listarBajoStock($limiteMinimo = 5) {
        $sql = "SELECT id_producto, codigo, nombre, id_categoria, precio, stock, estado 
                FROM producto 
                WHERE stock <= ? AND estado = 1";
        $filas = $this->conexion->get_records($sql, [(int)$limiteMinimo]);
        
        $listaProductos = [];
        foreach ($filas as $fila) {
            $listaProductos[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $listaProductos;
    }
}