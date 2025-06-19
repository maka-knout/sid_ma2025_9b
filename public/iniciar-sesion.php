<?php
require_once __DIR__ . '/servidor/dirs.php'; // Ajusta según ruta real
require_once SERVER_PATH . 'funciones.php';

redirigirSiLogeado();  // Si ya hay sesión, redirige al dashboard
procesar_login();      // Procesa login si envían formulario
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Iniciar Sesión - ADSO</title>
    <meta name="description" content="Formulario de acceso al sistema de eventos" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Noto+Serif+Dogra&display=swap" />
    <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body id="page-top">
    <?php require_once TEMPLATES_PATH . 'navbar.php'; ?>

    <main>
        <section class="position-relative py-4 py-xl-5">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-8 col-xl-6 text-center mx-auto">
                        <h2>Iniciar Sesión</h2>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6 col-xl-4">
                        <div class="card mb-5">
                            <?php
                                echo mostrar_mensaje_login();
                            ?>
                            <div class="card-body d-flex flex-column align-items-center">
                                <div class="bs-icon-xl bs-icon-circle bs-icon-primary bs-icon my-4">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                                <form method="POST" id="login_user">
                                    <div class="mb-3">
                                        <label for="correo">Correo electrónico</label>
                                        <input class="form-control form-control-lg" type="email" name="correo" id="correo" placeholder="ejemplo@gmail.com" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="clave">Contraseña</label>
                                        <div class="input-group">
                                            <input class="form-control form-control-lg" type="password" name="clave" id="clave" placeholder="********" minlength="8" required />
                                            <button class="btn btn-outline-secondary" type="button" id="ver_clave"><i class="fas fa-eye"></i></button>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100" type="submit" name="login_user">Iniciar sesión</button>
                                    </div>
                                </form>
                                <small>¿No tienes cuenta? <a href="registrarse.php">→ Registrarse</a></small><br />
                                <small>¿Olvidaste tu contraseña? <a href="recuperar-contrasena.php">→ Recuperar contraseña</a></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php require_once TEMPLATES_PATH . 'footer.php'; ?>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <script>
        const verClaveBtn = document.querySelector('#ver_clave');
        const claveInput = document.querySelector('#clave');

        verClaveBtn.addEventListener('click', () => {
            const type = claveInput.type === 'password' ? 'text' : 'password';
            claveInput.type = type;
            verClaveBtn.innerHTML = type === 'password'
                ? '<i class="fas fa-eye"></i>'
                : '<i class="fas fa-eye-slash"></i>';
        });
    </script>
</body>
</html>
