<?php

// Incluir la capa de datos de productos y la entidad correspondiente
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Datos' . DIRECTORY_SEPARATOR . 'ProductoDatos.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Producto.php';

class ProductoNegocio {
    private $productoDatos;

    public function __construct() {
        $this->productoDatos = new ProductoDatos();
    }

    // Registrar un producto validando campos obligatorios 
    public function registrarProducto(Producto $producto) {
        if (empty($producto->getCodigo()) || empty($producto->getNombre()) || empty($producto->getIdCategoria())) {
            return "El código, nombre y categoría son campos obligatorios.";
        }
        
        if ($producto->getPrecio() < 0 || $producto->getStock() < 0) {
            return "El precio y el stock no pueden ser valores negativos.";
        }
        
        return $this->productoDatos->insertar($producto);
    }

    // Modificar un producto existente
    public function modificarProducto(Producto $producto) {
        if (empty($producto->getIdProducto()) || empty($producto->getCodigo()) || empty($producto->getNombre())) {
            return "Datos insuficientes para actualizar el producto.";
        }
        
        if ($producto->getPrecio() < 0 || $producto->getStock() < 0) {
            return "El precio y el stock no pueden ser valores negativos.";
        }
        
        return $this->productoDatos->modificar($producto);
    }

    // Eliminar un producto por su ID 
    public function eliminarProducto($id_producto) {
        if (empty($id_producto)) {
            return false;
        }
        return $this->productoDatos->eliminar($id_producto);
    }

    // Listar todo el inventario
    public function listarProductos() {
        return $this->productoDatos->listarTodo();
    }

    // Obtener un producto específico por su ID
    public function obtenerPorId($id_producto) {
        if (empty($id_producto)) {
            return null;
        }
        return $this->productoDatos->buscarPorId($id_producto);
    }

    // Buscar productos por código o nombre 
    public function buscarProductos($busqueda) {
        if (empty($busqueda)) {
            return $this->productoDatos->listarTodo();
        }
        return $this->productoDatos->buscarPo iltro($busqueda);
    }


    // Obtener la lista de productos con existencias críticas
    public function obtenerBajoStock($limiteMinimo = 5) {
        return $this->productoDatos->listarBajoStock($limiteMinimo);
    }
}