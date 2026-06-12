<?php
/**
 * includes/sidebar.php — Sidebar de navegación reutilizable
 * Requiere: sesión activa con $_SESSION['nombre_usuario'] y $_SESSION['id_rol']
 * Variable esperada: $activePage (string) — nombre del módulo activo
 */
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Obtener el nombre de usuario y primera letra para el avatar
$nombreUsuario = $_SESSION['nombre_usuario'] ?? 'Usuario';
$iniciales     = strtoupper(substr($nombreUsuario, 0, 1));
$rolId         = $_SESSION['id_rol'] ?? 2;
if ($rolId == 1) {
    $rolNombre = 'Administrador';
} elseif ($rolId == 3) {
    $rolNombre = 'Supervisor';
} else {
    $rolNombre = 'Vendedor / Cajero';
}
$activePage    = $activePage ?? '';

// Contar productos con bajo stock para el badge
$badgeStock = 0;
try {
    require_once __DIR__ . '/../../Capa_Negocio/ProductoNegocio.php';
    $pNeg = new ProductoNegocio();
    $bajos = $pNeg->obtenerBajoStock(5);
    $badgeStock = count($bajos);
} catch (Exception $e) { $badgeStock = 0; }

// Definir los items de navegación
$navItems = [
    ['label' => 'Dashboard',    'icon' => 'fa-gauge-high',     'href' => '/Almacen_In/Capa_Presentacion/dashboard.php',         'key' => 'dashboard',  'section' => 'Principal'],
    ['label' => 'Categorías',   'icon' => 'fa-tags',           'href' => '/Almacen_In/Capa_Presentacion/categorias.php',        'key' => 'categorias', 'section' => 'Inventario'],
    ['label' => 'Productos',    'icon' => 'fa-boxes-stacked',  'href' => '/Almacen_In/Capa_Presentacion/productos.php',         'key' => 'productos',  'section' => 'Inventario', 'badge' => $badgeStock > 0 ? $badgeStock : 0],
    ['label' => 'Clientes',     'icon' => 'fa-address-book',   'href' => '/Almacen_In/Capa_Presentacion/clientes.php',         'key' => 'clientes',   'section' => 'Ventas'],
    ['label' => 'Nueva Venta',  'icon' => 'fa-cart-plus',      'href' => '/Almacen_In/Capa_Presentacion/ventas.php',           'key' => 'ventas',     'section' => 'Ventas'],
    ['label' => 'Historial',    'icon' => 'fa-receipt',        'href' => '/Almacen_In/Capa_Presentacion/historial_ventas.php', 'key' => 'historial',  'section' => 'Ventas'],
    ['label' => 'Usuarios',     'icon' => 'fa-users',          'href' => '/Almacen_In/Capa_Presentacion/usuarios.php',         'key' => 'usuarios',   'section' => 'Administración'],
];

// Agrupar por sección
$sections = [];
foreach ($navItems as $item) {
    $sections[$item['section']][] = $item;
}
?>
<!-- Overlay mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="mainSidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-text">
            <span class="brand-name" style="font-size: 14px; font-weight: 700; line-height: 1.3; color: var(--accent);">Control de Inventario &amp; Ventas</span>
            <span class="brand-sub">Sistema General</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <?php $lastSection = ''; ?>
        <?php foreach ($navItems as $item): ?>
            <?php if ($item['section'] !== $lastSection): ?>
                <div class="nav-section-label"><?php echo $item['section']; ?></div>
                <?php $lastSection = $item['section']; ?>
            <?php endif; ?>
            <a href="<?php echo $item['href']; ?>"
               class="nav-item <?php echo ($activePage === $item['key']) ? 'active' : ''; ?>"
               title="<?php echo $item['label']; ?>">
                <span><?php echo $item['label']; ?></span>
                <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                    <span class="nav-badge"><?php echo $item['badge']; ?></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Footer with user info -->
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar"><?php echo $iniciales; ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($nombreUsuario); ?></div>
                <div class="user-role"><?php echo $rolNombre; ?></div>
            </div>
            <a href="/Almacen_In/Capa_Presentacion/Controladores/LoginController.php?accion=logout"
               class="btn btn-danger btn-sm" style="margin-left: auto; padding: 4px 8px; font-size: 11px;" title="Cerrar Sesión">
                Salir
            </a>
        </div>
    </div>
</aside>

<script>
// Mobile sidebar toggle
const sidebar   = document.getElementById('mainSidebar');
const overlay   = document.getElementById('sidebarOverlay');
const hamburger = document.getElementById('hamburgerBtn');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    });
}
if (overlay) {
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
    });
}
</script>
