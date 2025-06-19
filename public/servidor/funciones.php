<?php
require_once __DIR__ . '/../clases/conexion.php';
require_once CLASS_PATH . 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function obtenerAuth(): Auth {
    static $auth = null;
    if ($auth === null) {
        $conexion = new Conexion();
        $auth = new Auth($conexion);
    }
    return $auth;
}

function redirigirSiLogeado(): void {
    if (!empty($_SESSION['usuario_id'])) {
        header('Location: dashboard.php'); // Cambia aquí si usas otra página tras login
        exit;
    }
}

function mostrar_mensaje_registro_extendido(): string {
    if (!empty($_SESSION['register_message'])) {
        $codigo = $_SESSION['register_message'];
        unset($_SESSION['register_message']);
        switch ($codigo) {
            case 1:
                return '<div class="alert alert-danger">Error en el registro. Inténtalo de nuevo.</div>';
            case 2:
                return '<div class="alert alert-warning">El correo ya está registrado.</div>';
            case 3:
                return '<div class="alert alert-success">Registro exitoso. Ahora puedes iniciar sesión.</div>';
            case 4:
                return '<div class="alert alert-warning">Las contraseñas no coinciden.</div>';
            case 5:
                return '<div class="alert alert-warning">La contraseña debe tener al menos 8 caracteres.</div>';
            default:
                return '';
        }
    }
    return '';
}

function mostrar_mensaje_login(): string {
    if (!empty($_SESSION['login_message'])) {
        $codigo = $_SESSION['login_message'];
        unset($_SESSION['login_message']);
        switch ($codigo) {
            case 1:
                return '<div class="alert alert-danger">Usuario no encontrado.</div>';
            case 2:
                return '<div class="alert alert-danger">Contraseña incorrecta.</div>';
            case 3:
                return '<div class="alert alert-success">Inicio de sesión exitoso.</div>';
            default:
                return '';
        }
    }
    return '';
}

function procesar_login(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_user'])) {
        $correo = trim($_POST['correo']);
        $clave = $_POST['clave'];

        $auth = obtenerAuth();
        if ($auth->logear_usuario($correo, $clave)) {
            header('Location: dashboard.php'); // Cambia si usas otra página tras login
            exit;
        } else {
            header('Location: iniciar-sesion.php');
            exit;
        }
    }
}

function procesar_registro(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_usuario'])) {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $correo = trim($_POST['correo']);
        $clave = $_POST['clave'];
        $confirmar_clave = $_POST['confirmar_clave'];

        if ($clave !== $confirmar_clave) {
            $_SESSION['register_message'] = 4; // Contraseñas no coinciden
            header('Location: registrarse.php');
            exit;
        }

        if (strlen($clave) < 8) {
            $_SESSION['register_message'] = 5; // Contraseña muy corta
            header('Location: registrarse.php');
            exit;
        }

        $auth = obtenerAuth();
        if ($auth->registrar_usuario($nombre, $apellido, $correo, $clave)) {
            header('Location: iniciar-sesion.php');
            exit;
        } else {
            header('Location: registrarse.php');
            exit;
        }
    }
}
