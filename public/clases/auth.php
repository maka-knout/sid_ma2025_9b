<?php
require_once 'conexion.php';

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
        $sqlVerificarCorreo = $this->conexion->prepare('SELECT id FROM usuarios WHERE correo = :correo');
        $sqlVerificarCorreo->bindParam(':correo', $correo, PDO::PARAM_STR);
        $sqlVerificarCorreo->execute();
        return $sqlVerificarCorreo->fetch() !== false;
    }

    // Registra un nuevo usuario
    public function registrar_usuario(string $nombre, string $apellido, string $correo, string $clave): bool
    {
        if ($this->verificar_correo($correo)) {
            $_SESSION['register_message'] = 2; // Correo ya registrado
            return false;
        }

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

    // Inicia sesión del usuario
    public function logear_usuario(string $correo, string $clave): bool
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

        // Guardar datos en sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['login_message'] = 3; // Login exitoso
        return true;
    }

    // Elimina un usuario por su correo
    public function eliminar_usuario(string $correo): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM usuarios WHERE correo = :correo');
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Guarda o actualiza el token de recuperación en la tabla tokens_recuperacion
    public function guardar_token(string $correo, string $token): bool
    {
        $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour')); // Expira en 1 hora

        // Verificar si ya existe un token para ese correo
        $sqlCheck = "SELECT id FROM tokens_recuperacion WHERE correo = :correo";
        $stmtCheck = $this->conexion->prepare($sqlCheck);
        $stmtCheck->execute([':correo' => $correo]);
        $existe = $stmtCheck->fetch();

        if ($existe) {
            // Actualizar token y expiración
            $sqlUpdate = "UPDATE tokens_recuperacion SET token = :token, creado_en = NOW(), expiracion = :expiracion WHERE correo = :correo";
            $stmtUpdate = $this->conexion->prepare($sqlUpdate);
            return $stmtUpdate->execute([
                ':token' => $token,
                ':expiracion' => $expiracion,
                ':correo' => $correo
            ]);
        } else {
            // Insertar nuevo registro
            $sqlInsert = "INSERT INTO tokens_recuperacion (correo, token, creado_en, expiracion) VALUES (:correo, :token, NOW(), :expiracion)";
            $stmtInsert = $this->conexion->prepare($sqlInsert);
            return $stmtInsert->execute([
                ':correo' => $correo,
                ':token' => $token,
                ':expiracion' => $expiracion
            ]);
        }
    }

    // Valida que el token exista y no haya expirado
    public function validar_token(string $token): bool
    {
        $sql = "SELECT id FROM tokens_recuperacion WHERE token = :token AND expiracion > NOW()";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch() !== false;
    }

    // Actualiza la contraseña usando el token de recuperación
    public function actualizar_contraseña_por_token(string $token, string $nuevaClave): bool
    {
        // Buscar correo asociado al token válido
        $sql = "SELECT correo FROM tokens_recuperacion WHERE token = :token AND expiracion > NOW()";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':token' => $token]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resultado) {
            return false;
        }

        $correo = $resultado['correo'];
        $clave_encriptada = password_hash($nuevaClave, PASSWORD_DEFAULT);

        // Actualizar contraseña en la tabla usuarios
        $sqlUpdate = "UPDATE usuarios SET clave = :clave WHERE correo = :correo";
        $stmtUpdate = $this->conexion->prepare($sqlUpdate);
        $updateExito = $stmtUpdate->execute([
            ':clave' => $clave_encriptada,
            ':correo' => $correo
        ]);

        if ($updateExito) {
            // Borrar token ya usado
            $sqlDelete = "DELETE FROM tokens_recuperacion WHERE token = :token";
            $stmtDelete = $this->conexion->prepare($sqlDelete);
            $stmtDelete->execute([':token' => $token]);
            return true;
        }

        return false;
    }
}
