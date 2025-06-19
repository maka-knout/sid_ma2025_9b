<?php
class Conexion {
    private $host = 'maria.11';  // Cambia por 'localhost' si corresponde
    private $db = 'eventos';
    private $user = 'root';
    private $pass = 'root';
    private $charset = 'utf8mb4';

    public function conectar() {
        try {
            $dsn = "mysql:host=eventos-db;dbname=eventos;charset=utf8mb4";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
