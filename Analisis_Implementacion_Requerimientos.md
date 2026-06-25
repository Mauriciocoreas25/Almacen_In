# Análisis de la Resolución de Requerimientos — Sistema de Control de Inventario y Ventas

Este documento explica, en un lenguaje sencillo y no técnico, cómo se implementaron y resolvieron cada uno de los requerimientos solicitados para el desarrollo del **Sistema de Control de Inventario y Ventas** del almacén.

---

## 1. Estructura y Organización del Sistema (Arquitectura Limpia)
Para cumplir con la solicitud de un **desarrollo ordenado y modular**, el sistema se diseñó dividiendo el programa en cuatro "capas" o compartimentos principales. De esta manera, si en el futuro se desea cambiar la base de datos o el diseño de las pantallas, se puede hacer de forma aislada sin romper el resto del sistema:

*   **Pantallas e Interfaz (Presentación):** Contiene todo lo que el usuario ve y con lo que interactúa en su pantalla (formularios, botones, tablas y reportes).
*   **Reglas del Negocio (Capa Lógica):** Funciona como el "cerebro" del sistema. Aquí se decide si una venta se puede realizar (validación de stock) y se calcula de forma automática el impuesto y el total.
*   **Acceso a Datos:** Es el intermediario directo con la base de datos, encargado únicamente de guardar, modificar, leer y borrar información.
*   **Modelos de Información (Entidades):** Representan los objetos reales del negocio, como un *Producto*, un *Cliente*, un *Usuario* o una *Venta*.

---

## 2. Acceso Seguro y Niveles de Usuario (Roles)
Se implementó un sistema de inicio de sesión seguro que controla estrictamente quién tiene permiso para ver o modificar la información:

*   **Autenticación Protegida:** Los usuarios deben ingresar con su cuenta y contraseña. Por seguridad, **las contraseñas se guardan encriptadas** en la base de datos; esto significa que ni el programador ni los administradores pueden ver la clave original de un usuario en texto plano.
*   **Control de Sesión:** Si un usuario cierra la sesión o pasa mucho tiempo inactivo, el sistema lo desconecta de forma automática para evitar que personas no autorizadas accedan a la información.
*   **Los Tres Niveles de Acceso (Roles):**
    1.  **Administrador:** Tiene control total. Puede ver los reportes, crear categorías, añadir productos, gestionar clientes, realizar ventas y administrar usuarios (crear nuevos empleados, modificar sus datos o desactivarlos).
    2.  **Supervisor:** Orientado a la gestión física del almacén. Puede ver el panel general (dashboard), administrar categorías y productos, gestionar clientes y realizar ventas, pero **no puede acceder** a la sección de administración de usuarios.
    3.  **Vendedor / Cajero:** Su interfaz está simplificada para el día a día. Solo tiene permitido registrar clientes, realizar ventas, consultar el historial de ventas y ver el dashboard general. Las opciones de inventario (productos, categorías) y usuarios están completamente ocultas para él.

---

## 3. Administración del Negocio (Módulos CRUD completos)
El sistema permite realizar todas las operaciones básicas de gestión (Crear, Leer, Actualizar y Eliminar) para cada módulo, aplicando filtros y validaciones inteligentes:

*   **Módulo de Clientes:** Permite registrar tanto a clientes particulares (personas naturales con DUI) como a empresas o negocios (personas jurídicas con NIT, NRC y Giro comercial). El sistema valida que los campos clave no queden en blanco.
*   **Módulo de Categorías:** Facilita la clasificación de productos (por ejemplo: "Laptops", "Accesorios", "Audio"). Si una categoría ya no se usa, puede desactivarse.
*   **Módulo de Productos (Inventario):** Cada producto se registra con un código único, nombre, su categoría respectiva, precio de venta, stock disponible y estado (activo/inactivo). El sistema impide que se guarden precios o existencias con valores negativos.
*   **Módulo de Usuarios:** Permite dar de alta a los empleados del almacén, asignarles su rol de trabajo y cambiar su estado. En lugar de eliminar permanentemente a un usuario que ya no trabaja en la empresa (lo que rompería el historial de ventas que atendió), el sistema permite **desactivarlo**, bloqueando su acceso pero manteniendo sus registros históricos.

---

## 4. El Proceso de Ventas (Punto de Venta - POS)
El módulo de facturación y cobro fue diseñado para ser ágil e interactivo:

*   **Venta Multi-Producto:** En una sola transacción, el cajero puede agregar múltiples productos, especificando diferentes cantidades para cada uno.
*   **Cálculo Automático de Montos:** A medida que se agregan artículos al carrito, el sistema calcula de forma automática el subtotal, el impuesto (IVA del 13% correspondiente a El Salvador) y el gran total a pagar, mostrando la información clara al instante.
*   **Clientes Registrados o Venta Directa:** Se puede asociar la venta a un cliente específico registrado en el sistema o realizar una venta directa al público en general.
*   **Generación de Comprobantes:** Al finalizar la venta, se genera un comprobante o recibo digital detallado con el desglose de productos y totales. Este recibo cuenta con un formato limpio optimizado para **imprimirse o guardarse directamente en formato PDF**.

---

## 5. Reglas de Control de Inventario (Gestión Automática de Stock)
Se implementaron reglas estrictas en el código para asegurar la exactitud del inventario en todo momento:

*   **Protección contra Sobreventas (Sin existencias):** El sistema verifica el stock en tiempo real. Si un cliente solicita una cantidad mayor a la disponible en el almacén, el sistema bloquea la transacción e indica con precisión qué producto no tiene suficiente stock.
*   **Reducción Automática en Venta:** En el momento exacto en que se confirma la venta, las cantidades compradas se restan del stock general del almacén de manera automática.
*   **Devolución al Inventario por Anulación:** Si una venta debe ser cancelada o anulada, el sistema cambia su estado a "Anulada" y **devuelve automáticamente** los productos al almacén, restaurando el stock a su cantidad original antes de la venta.

---

## 6. Reportes y Panel de Control (Dashboard)
Para facilitar la toma de decisiones del negocio, el panel principal (Dashboard) resume los indicadores más importantes de forma visual y en tiempo real:

*   **Alerta de Stock Crítico:** Muestra de forma destacada una lista con los productos que tienen **5 o menos unidades disponibles**, permitiendo que el encargado sepa exactamente qué artículos debe reabastecer urgentemente.
*   **Ingresos y Ventas por Período:** Permite elegir un rango de fechas (desde/hasta) para ver cuántas ventas se hicieron y cuánto dinero total ingresó en ese período específico.
*   **Productos Más Vendidos (Ranking):** Presenta una tabla ordenada con los productos más populares y la cantidad total de unidades vendidas de cada uno, ayudando a identificar los productos estrella del almacén.

---

## 7. Diseño Adaptable y Modernidad Visual
*   **Diseño Limpio y Profesional:** El sistema utiliza una paleta de colores moderna y tipografía estilizada que facilita la lectura y reduce la fatiga visual de los empleados durante su jornada laboral.
*   **Diseño Adaptable (Responsive):** La interfaz fue optimizada utilizando tecnologías estándar para que se ajuste automáticamente a cualquier tamaño de pantalla. El sistema puede operarse cómodamente desde una computadora de escritorio en la caja del almacén, una tablet o un teléfono inteligente en el pasillo de inventario.
