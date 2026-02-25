<!DOCTYPE html>
<html lang="es">
    @php
        $user = Auth::user();
        $rolePermissions = $user && $user->role
            ? $user->role->permissions->mapWithKeys(static fn($perm) => [$perm->modulo . '.' . $perm->tipo_permiso => true])
            : collect();
        $hasPermission = static function (string $module, string $action = 'mostrar') use ($user, $rolePermissions): bool {
            if (!$user) {
                return false;
            }
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }
            return (bool) $rolePermissions->get($module . '.' . $action, false);
        };
        $moduleAccess = static function (string $module) use ($hasPermission): bool {
            foreach (['mostrar', 'crear', 'editar', 'eliminar'] as $action) {
                if ($hasPermission($module, $action)) {
                    return true;
                }
            }
            return false;
        };
        $canUserSystem = $moduleAccess('usuarios') || $moduleAccess('roles') || $moduleAccess('permisos');
    @endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Eunomia CMS - Panel de Administración')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        .sidebar-dark-primary .nav-sidebar > .nav-item > .nav-link.active {
            background-color: #007bff;
        }
        .content-wrapper {
            background-color: #f4f4f4;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }
        .main-header {
            z-index: 1037;
        }
    </style>
    
    @yield('css')
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('inicio', 'es') }}" target="_blank" class="nav-link">
                        <i class="fas fa-external-link-alt"></i> Ver Sitio
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i>
                        {{ Auth::user()->name ?? 'Usuario' }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li>
                            <h6 class="dropdown-header">Sesión de usuario</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i> Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>

        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="d-none">
            @csrf
        </form>

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <i class="fas fa-theater-masks brand-image elevation-3" style="margin-left: .8rem; margin-top: -4px; color: #fff;"></i>
                <span class="brand-text font-weight-light">Eunomia CMS</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    @php
                        $user = Auth::user();
                        $rolePermissions = $user && $user->role
                            ? $user->role->permissions->mapWithKeys(static fn($perm) => [$perm->modulo . '.' . $perm->tipo_permiso => true])
                            : collect();
                        $hasPermission = static function (string $module, string $action = 'mostrar') use ($user, $rolePermissions): bool {
                            if (!$user) {
                                return false;
                            }
                            if ($user->isAdmin()) {
                                return true;
                            }
                            return (bool) $rolePermissions->get($module . '.' . $action, false);
                        };
                        $moduleAccess = static function (string $module) use ($hasPermission): bool {
                            foreach (['mostrar', 'crear', 'editar', 'eliminar'] as $action) {
                                if ($hasPermission($module, $action)) {
                                    return true;
                                }
                            }
                            return false;
                        };
                        $canUserSystem = $moduleAccess('usuarios') || $moduleAccess('roles') || $moduleAccess('permisos');
                    @endphp
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        @if($moduleAccess('contenidos'))
                            <li class="nav-item {{ request()->routeIs('admin.contents.*') || request()->routeIs('admin.tipos-contenido.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('admin.contents.*') || request()->routeIs('admin.tipos-contenido.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-newspaper"></i>
                                    <p>
                                        Contenidos
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('contenidos', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.contents.index') }}" class="nav-link {{ request()->routeIs('admin.contents.index') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Todos los Contenidos</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('contenidos', 'crear'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.contents.create') }}" class="nav-link {{ request()->routeIs('admin.contents.create') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Crear Contenido</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('contenidos', 'editar') || $hasPermission('contenidos', 'crear'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.tipos-contenido.index') }}" class="nav-link {{ request()->routeIs('admin.tipos-contenido.*') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Tipos de Contenido</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($moduleAccess('galerias'))
                            <li class="nav-item {{ request()->routeIs('admin.galleries.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('admin.galleries.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-images"></i>
                                    <p>
                                        Galerías
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('galerias', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.galleries.index') }}" class="nav-link {{ request()->routeIs('admin.galleries.index') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Todas las Galerías</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('galerias', 'crear'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.galleries.create') }}" class="nav-link {{ request()->routeIs('admin.galleries.create') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Nueva Galería</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($moduleAccess('idiomas'))
                            <li class="nav-item {{ request()->routeIs('admin.idiomas.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('admin.idiomas.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-language"></i>
                                    <p>
                                        Idiomas
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('idiomas', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.idiomas.index') }}" class="nav-link {{ request()->routeIs('admin.idiomas.index') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Gestionar Idiomas</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('idiomas', 'crear'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.idiomas.create') }}" class="nav-link {{ request()->routeIs('admin.idiomas.create') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Nuevo Idioma</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($moduleAccess('menus'))
                            <li class="nav-item {{ request()->routeIs('admin.menus.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-bars"></i>
                                    <p>
                                        Menús
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('menus', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.menus.index') }}" class="nav-link {{ request()->routeIs('admin.menus.index') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Gestionar Menús</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($moduleAccess('slides'))
                            <li class="nav-item {{ request()->routeIs('admin.slides.*') ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs('admin.slides.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-images"></i>
                                    <p>
                                        Slides
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('slides', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.slides.index') }}" class="nav-link {{ request()->routeIs('admin.slides.index') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Gestionar Slides</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('slides', 'crear'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.slides.create') }}" class="nav-link {{ request()->routeIs('admin.slides.create') ? 'active' : '' }}">
                                                <i class="far fa-circle nav-icon"></i>
                                                <p>Crear Slide</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($moduleAccess('imagenes'))
                            <li class="nav-item {{ request()->routeIs(['admin.image-configs.*','admin.configuracion_empresa.*']) ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs(['admin.image-configs.*','admin.configuracion_empresa.*']) ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-cogs"></i>
                                    <p>
                                        Configuración
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('imagenes', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.image-configs.index') }}" class="nav-link {{ request()->routeIs('admin.image-configs.*') ? 'active' : '' }}">
                                                <i class="far fa-image nav-icon"></i>
                                                <p>Configuración de Imágenes</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('admin'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.configuracion_empresa.edit') }}" class="nav-link {{ request()->routeIs('admin.configuracion_empresa.edit') ? 'active' : '' }}">
                                                <i class="fas fa-building nav-icon"></i>
                                                <p>Gestor de empresa</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif

                        @if($canUserSystem)
                            <li class="nav-item {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'menu-open' : '' }}">
                                <a href="#" class="nav-link {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-users-cog"></i>
                                    <p>
                                        Sistema de Usuarios
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    @if($hasPermission('usuarios', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                                <i class="fas fa-user nav-icon"></i>
                                                <p>Usuarios</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('roles', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                                <i class="fas fa-user-tag nav-icon"></i>
                                                <p>Roles</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('permisos', 'mostrar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.permissions.index') }}" class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                                <i class="fas fa-key nav-icon"></i>
                                                <p>Permisos</p>
                                            </a>
                                        </li>
                                    @endif
                                    @if($hasPermission('roles', 'mostrar') || $hasPermission('roles', 'editar'))
                                        <li class="nav-item">
                                            <a href="{{ route('admin.roles.permission-matrix') }}" class="nav-link {{ request()->routeIs('admin.roles.permission-matrix*') ? 'active' : '' }}">
                                                <i class="fas fa-table nav-icon"></i>
                                                <p>Matriz de Permisos</p>
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Mensajes Flash -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-check"></i> Éxito</h5>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h5><i class="icon fas fa-ban"></i> Error</h5>
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; {{ date('Y') }} <a href="#">Eunomia CMS</a>.</strong>
            Sistema de gestión de contenidos para Nuntris Teatro.
            <div class="float-right d-none d-sm-inline-block">
                <b>Versión</b> 1.0.0
            </div>
        </footer>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <!-- TinyMCE (Self-hosted) -->
    <script src="{{ asset('tinymce/js/tinymce/tinymce.min.js') }}"></script>
    <!-- TinyMCE Configuration -->
    <script src="{{ asset('js/tinymce-config.js') }}"></script>
    
    @yield('scripts')
    @stack('scripts')
</body>
</html>