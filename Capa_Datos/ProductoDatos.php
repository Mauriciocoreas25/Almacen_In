<?php

// Incluir la clase de conexión y la entidad Producto
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Conexion.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Capa_Entidades' . DIRECTORY_SEPARATOR . 'Producto.php';

/**
 * ProductoDatos — Adaptado al schema real de la BD.
 * La tabla 'producto' usa 'precio_venta' (no 'precio').
 * El campo 'estado' es BIT(1).
 */
class ProductoDatos {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
    }

    // Insertar nuevo producto
    public function insertar(Producto $producto) {
        $sql = "INSERT INTO producto (id_categoria, codigo, nombre, precio_venta, stock, estado)
                VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $producto->getIdCategoria(),
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getEstado() ? 1 : 0
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Modificar producto existente
    public function modificar(Producto $producto) {
        $sql = "UPDATE producto SET id_categoria = ?, codigo = ?, nombre = ?, precio_venta = ?, stock = ?, estado = ?
                WHERE id_producto = ?";
        $params = [
            $producto->getIdCategoria(),
            $producto->getCodigo(),
            $producto->getNombre(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getEstado() ? 1 : 0,
            $producto->getIdProducto()
        ];
        return $this->conexion->execute_query($sql, $params);
    }

    // Eliminar producto
    public function eliminar($id_producto) {
        $sql    = "DELETE FROM producto WHERE id_producto = ?";
        $params = [$id_producto];
        return $this->conexion->execute_query($sql, $params);
    }

    // Verificar si el producto tiene ventas asociadas
    public function tieneVentasAsociadas($id_producto) {
        $sql  = "SELECT COUNT(*) as total FROM detalle_venta WHERE id_producto = ?";
        $fila = $this->conexion->get_record($sql, [$id_producto]);
        return $fila && (int)$fila['total'] > 0;
    }

    // Listar todo el inventario
    public function listarTodo() {
        $sql  = "SELECT id_producto, id_categoria, codigo, nombre, precio_venta, stock, estado FROM producto ORDER BY id_producto DESC";
        $filas = $this->conexion->get_records($sql);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio_venta'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $lista;
    }

    // Buscar producto por ID
    public function buscarPorId($id_producto) {
        $sql  = "SELECT id_producto, id_categoria, codigo, nombre, precio_venta, stock, estado FROM producto WHERE id_producto = ?";
        $fila = $this->conexion->get_record($sql, [$id_producto]);

        if ($fila) {
            return new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio_venta'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return null;
    }

    // Buscar productos por código o nombre
    public function buscarPorFiltro($busqueda) {
        $sql     = "SELECT id_producto, id_categoria, codigo, nombre, precio_venta, stock, estado
                    FROM producto
                    WHERE nombre LIKE ? OR codigo LIKE ?";
        $termino = "%" . $busqueda . "%";
        $filas   = $this->conexion->get_records($sql, [$termino, $termino]);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio_venta'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $lista;
    }

    // Actualizar stock (suma positiva o negativa)
    public function actualizarStock($id_producto, $cantidad) {
        $sql    = "UPDATE producto SET stock = stock + (?) WHERE id_producto = ?";
        $params = [(int)$cantidad, $id_producto];
        return $this->conexion->execute_query($sql, $params);
    }

    // Reporte: productos con bajo stock
    public function listarBajoStock($limiteMinimo = 5) {
        $sql  = "SELECT id_producto, id_categoria, codigo, nombre, precio_venta, stock, estado
                 FROM producto
                 WHERE stock <= ? AND estado = 1
                 ORDER BY stock ASC";
        $filas = $this->conexion->get_records($sql, [(int)$limiteMinimo]);

        $lista = [];
        foreach ($filas as $fila) {
            $lista[] = new Producto(
                $fila['id_producto'],
                $fila['codigo'],
                $fila['nombre'],
                $fila['id_categoria'],
                (double)$fila['precio_venta'],
                (int)$fila['stock'],
                (bool)$fila['estado']
            );
        }
        return $lista;
    }
}