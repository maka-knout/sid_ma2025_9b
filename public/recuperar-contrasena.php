<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir constantes de rutas
require_once __DIR__ . '/servidor/dirs.php';

// Incluir archivos necesarios
require_once CLASS_PATH . 'auth.php';
require_once SERVER_PATH . 'funciones.php';

// Redirigir si el usuario ya está logueado
redirigirSiLogeado();

// Crear instancia de Auth con conexión a base de datos
$auth = new Auth(new Conexion());

// Procesar formulario de recuperación
if (isset($_POST['recuperar_contrasena'])) {
    $correo = trim($_POST['correo']);

    if ($auth->verificar_correo($correo)) {
        // Generar token seguro
        $token = bin2hex(random_bytes(16));
        $auth->guardar_token($correo, $token);

        // Construir URL para restablecer contraseña (ajusta dominio y puerto si necesario)
        $url = "http://localhost:8080/restablecer-contrasena.php?token=$token";

        // Guardar mensaje con enlace para mostrar en la interfaz
        $_SESSION['pswdrst'] = "Enlace generado para restablecer contraseña:<br><a href='$url'>$url</a>";
    } else {
        // Por seguridad no indicar si correo existe o no
        $_SESSION['pswdrst'] = "Si el correo existe, hemos enviado un enlace para restablecer tu contraseña.";
    }

    // Redirigir para evitar reenvío al refrescar
    header('Location: recuperar-contrasena.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Recuperar Contraseña - ADSO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
</head>
<body>
    <?php require_once TEMPLATES_PATH . 'navbar.php'; ?>

    <main class="container py-5">
        <h2 class="mb-4 text-center">Recuperar Contraseña</h2>

        <?php
        if (!empty($_SESSION['pswdrst'])) {
            echo '<div class="alert alert-info">' . $_SESSION['pswdrst'] . '</div>';
            unset($_SESSION['pswdrst']);
        }
        ?>

        <form method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="correo" class="form-label">Ingresa tu correo electrónico</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@gmail.com" required />
            </div>
            <button type="submit" name="recuperar_contrasena" class="btn btn-primary w-100">Recuperar Contraseña</button>
        </form>
    </main>

    <?php require_once TEMPLATES_PATH . 'footer.php'; ?>
</body>
</html>
