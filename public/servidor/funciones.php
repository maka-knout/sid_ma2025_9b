<?php
/** @noinspection PhpUnusedLocalVariableInspection */
/** @noinspection PhpRedundantOptionalArgumentInspection */

(session_status() === PHP_SESSION_NONE ? session_start() : ''); // Iniciar la sesión si no está iniciada
require 'dirs.php'; // Archivo con rutas base
require_once(CLASS_PATH . 'conexion.php'); // Clase Conexion
require_once(CLASS_PATH . 'auth.php'); // Clase Auth
require_once(SERVER_PATH . 'helpers.php'); // Funciones auxiliares
require_once(SERVER_PATH . 'msg.php'); // Mensajes de alerta

$url = $_SERVER['REQUEST_URI']; // URL actual

// Función para iniciar sesión
function logearUsuario($correo, $clave): bool
{
    $auth = obtenerAuth();
    return $auth->logear_usuario($correo, $clave);
}

// Procesar formulario login
if (isset($_POST['login_user'])) {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    $login = logearUsuario($correo, $clave);
}

// Función para registrar usuario
function registrarUsuario($nombre, $apellido, $correo, $clave): bool
{
    $auth = obtenerAuth();
    return $auth->registrar_usuario($nombre, $apellido, $correo, $clave);
}

// Procesar formulario registro
if (isset($_POST['registrar_usuario'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    $registro = registrarUsuario($nombre, $apellido, $correo, $clave);
}

// Verificar si está logeado
function verificarLogin(): bool
{
    return isset($_SESSION['usuario_id']);
}

// Obtener datos de sesión
function obtenerDatosUsuario(): array
{
    $id_usuario = $_SESSION['usuario_id'];
    $nombre = $_SESSION['nombre'];
    $apellido = $_SESSION['apellido'];
    $correo = $_SESSION['correo'];
    return [$id_usuario, $nombre, $apellido, $correo];
}

// Redirigir si ya está logeado
function redirigirSiLogeado(): void
{
    if (verificarLogin()) {
        header('Location: inicio.php');
        exit();
    }
}

// Cerrar sesión
function cerrarSesion(): bool
{
    session_unset();
    session_destroy();
    return true;
}

if (isset($_POST['cerrar_sesion'])) {
    $_SESSION['logout_message'] = cerrarSesion() ? 1 : 2;
}
