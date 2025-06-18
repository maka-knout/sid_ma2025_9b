<?php
class Conexion {
    private $host = 'maria.11';
    private $db = 'eventos';
    private $user = 'root';
    private $pass = 'root';
    private $charset = 'utf8mb4';

    public function conectar() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
