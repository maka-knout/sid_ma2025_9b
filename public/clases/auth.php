<?php
// Incluir la clase de conexión a la base de datos
require_once 'conexion.php';

// Clase para autenticar usuarios
class Auth
{
    protected PDO $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion->conectar();
    }

    // Verifica si el correo ya existe en la base de datos
    public function verificar_correo(string $correo): bool
    {
        $sqlVerificarCorreo = $this->conexion->prepare('SELECT id, nombre, apellido, correo, clave FROM usuarios WHERE correo = :correo');
        $sqlVerificarCorreo->bindParam(':correo', $correo, PDO::PARAM_STR);
        $sqlVerificarCorreo->execute();
        return $sqlVerificarCorreo->fetch() !== false;
    }

    // Registra un nuevo usuario
    public function registrar_usuario($nombre, $apellido, $correo, $clave): bool
    {
        $correoExiste = $this->verificar_correo($correo);

        if ($correoExiste) {
            $_SESSION['register_message'] = 2; // Correo ya registrado
            return false;
        } else {
            $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

            $stmt = $this->conexion->prepare('INSERT INTO usuarios (nombre, apellido, correo, clave) VALUES (:nombre, :apellido, :correo, :clave)');
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':clave', $clave_encriptada, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $_SESSION['register_message'] = 3; // Registro exitoso
                return true;
            } else {
                $_SESSION['register_message'] = 1; // Error en la consulta
                return false;
            }
        }
    }

    // Inicia sesión del usuario
    public function logear_usuario($correo, $clave): bool
    {
        $stmt = $this->conexion->prepare('SELECT * FROM usuarios WHERE correo = :correo');
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $_SESSION['login_message'] = 1; // Usuario no encontrado
            return false;
        }

        if (!password_verify($clave, $usuario['clave'])) {
            $_SESSION['login_message'] = 2; // Contraseña incorrecta
            return false;
        }

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['login_message'] = 3; // Login exitoso
        return true;
    }

    // Elimina un usuario por su correo
    public function eliminar_usuario($correo): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM usuarios WHERE correo = :correo');
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
