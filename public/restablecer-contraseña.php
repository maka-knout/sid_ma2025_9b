<?php
require_once __DIR__ . '/servidor/dirs.php';
require_once CLASS_PATH . 'auth.php';
require_once SERVER_PATH . 'funciones.php';

session_start();

$auth = new Auth(new Conexion());

// Redirigir si ya está logueado
redirigirSiLogeado();

// Obtener token de la URL
$token = $_GET['token'] ?? '';

// Validar token
if (!$token || !$auth->validar_token($token)) {
    $_SESSION['reset_error'] = "Token inválido o expirado.";
    header('Location: iniciar-sesion.php');
    exit;
}

$mensaje = '';
if (isset($_POST['restablecer_password'])) {
    $clave = $_POST['clave'] ?? '';
    $clave_confirmar = $_POST['clave_confirmar'] ?? '';

    if (empty($clave) || empty($clave_confirmar)) {
        $mensaje = "Ambos campos de contraseña son obligatorios.";
    } elseif ($clave !== $clave_confirmar) {
        $mensaje = "Las contraseñas no coinciden.";
    } elseif (strlen($clave) < 8) {
        $mensaje = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        // Actualizar contraseña
        if ($auth->actualizar_contraseña_por_token($token, $clave)) {
            $_SESSION['reset_success'] = "Contraseña actualizada correctamente. Ahora puedes iniciar sesión.";
            header('Location: iniciar-sesion.php');
            exit;
        } else {
            $mensaje = "Error al actualizar la contraseña. Intenta de nuevo.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Restablecer Contraseña - ADSO</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Restablecer Contraseña</h2>

        <?php if ($mensaje): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <form method="POST" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="clave" class="form-label">Nueva contraseña</label>
                <input type="password" name="clave" id="clave" class="form-control" minlength="8" required />
            </div>
            <div class="mb-3">
                <label for="clave_confirmar" class="form-label">Confirmar nueva contraseña</label>
                <input type="password" name="clave_confirmar" id="clave_confirmar" class="form-control" minlength="8" required />
            </div>
            <button type="submit" name="restablecer_password" class="btn btn-primary w-100">Cambiar contraseña</button>
        </form>
        <div class="mt-3 text-center">
            <a href="iniciar-sesion.php">Volver a iniciar sesión</a>
        </div>
    </div>

    <script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
