CREATE DATABASE IF NOT EXISTS `eventos` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `eventos`;

CREATE TABLE usuarios
(
    ID_Usuario        INT AUTO_INCREMENT PRIMARY KEY,
    Cod_Usuario       VARCHAR(10) NOT NULL UNIQUE,
    Nombre            VARCHAR(200) NOT NULL,
    Apellido          VARCHAR(200) NOT NULL,
    Correo            VARCHAR(150) NOT NULL UNIQUE,
    Clave             CHAR(60) NOT NULL,
    Fecha_De_Registro DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    INDEX idx_usuarios_correo (Correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE eventos
(
    ID_Evento          INT AUTO_INCREMENT PRIMARY KEY,
    Nombre_Evento      VARCHAR(200) NOT NULL,
    Descripcion_Evento VARCHAR(500) NOT NULL,
    Fecha_De_Registro  DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    Lugar              VARCHAR(200) NOT NULL,
    Fecha_Y_Hora       DATETIME NOT NULL,
    INDEX idx_eventos_nombre (Nombre_Evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE eventos_eliminados
(
    ID_Evento            INT PRIMARY KEY,
    Nombre_Evento        VARCHAR(200) NOT NULL,
    Descripcion_Evento   VARCHAR(500) NOT NULL,
    Fecha_De_Eliminacion DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    Lugar                VARCHAR(200) NOT NULL,
    Fecha_Y_Hora         DATETIME NOT NULL,
    INDEX idx_eventos_nombre (Nombre_Evento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE restablecer_contrasena
(
    ID                 INT AUTO_INCREMENT PRIMARY KEY,
    Correo             VARCHAR(150) NOT NULL,
    Token              CHAR(60) NOT NULL,
    Fecha_De_Solicitud DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    INDEX idx_restablecer_contrasena_token (Token),
    CONSTRAINT fk_restablecer_contrasena_usuarios FOREIGN KEY (Correo) REFERENCES usuarios(Correo) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- DROP TRIGGER IF EXISTS Cod_Usuario;

-- CREATE TRIGGER Cod_Usuario BEFORE INSERT ON usuarios
-- FOR EACH ROW
-- BEGIN
   --  DECLARE next_val INT;
    -- SET next_val = (SELECT MAX(CAST(SUBSTRING(Cod_Usuario, 5) AS UNSIGNED)) + 1 FROM usuarios WHERE Cod_Usuario LIKE 'COD-%');
    -- IF next_val IS NULL THEN
     --   SET next_val = 10000;
    -- END IF;
   --  SET NEW.Cod_Usuario = CONCAT('COD-', LPAD(next_val, 5, '0'));
-- END;
