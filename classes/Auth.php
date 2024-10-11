<?php

namespace App;

use PDO;
use PDOException;

class Auth
{
    private $db;
    private const USERS_TABLE = 'users';
    private const LOGS_TABLE = 'user_logs';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function autenticar(string $email, string $password): bool
    {
        $usuario = $this->obtenerUsuarioPorEmail($email);
        if (!$usuario) {
            $this->logFailedAttempt($email);
            return false;
        }

        if (password_verify($password, $usuario['password']) && $usuario['estado'] === 'Activo') {
            $this->iniciarSesion($usuario['id']);
            return true;
        }

        $this->logFailedAttempt($email);
        return false;
    }

    public function obtenerUsuarioPorEmail(string $email): ?array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::USERS_TABLE . " WHERE email = :email LIMIT 1");
            $query->execute(['email' => $email]);
            return $query->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function logFailedAttempt(string $email): void
    {
        $ip = file_get_contents('https://api.ipify.org');

        try {
            $query = $this->db->prepare("INSERT INTO " . self::LOGS_TABLE . " (email, ip, type) VALUES (:email, :ip, 'FAILED_ATTEMPT')");
            $query->execute(['email' => $email, 'ip' => $ip]);
        } catch (PDOException $e) {
            // Manejo de errores
        }
    }

    private function iniciarSesion(int $userId): void
    {
        $_SESSION['user'] = $userId;
    }

    public function isLogged(): bool
    {
        return isset($_SESSION['user']);
    }

    public function updateFirstTime(string $email): bool
    {
        try {
            $query = $this->db->prepare("UPDATE " . self::USERS_TABLE . " SET first_time = 0 WHERE email = :email");
            $query->execute(['email' => $email]);
            return $query->rowCount() === 1;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function crearUsuario(string $nombre, string $apellido, string $email): bool
    {
        $password = password_hash('barberia2024', PASSWORD_DEFAULT);

        try {
            $query = $this->db->prepare("INSERT INTO " . self::USERS_TABLE . " (nombre, apellido, email, password) VALUES (:nombre, :apellido, :email, :password)");
            $query->execute(['nombre' => $nombre, 'apellido' => $apellido, 'email' => $email, 'password' => $password]);
            return $query->rowCount() === 1;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener sólo el nombre por el id
    public function obtenerNombrePorId(int $id): ?string
    {
        try {
            $query = $this->db->prepare("SELECT nombre FROM " . self::USERS_TABLE . " WHERE id = :id LIMIT 1");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC)['nombre'] ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Obtener nombre y apellido por el id
    public function obtenerNombreYApellidoPorId(int $id): ?string
    {
        try {
            $query = $this->db->prepare("SELECT CONCAT(nombre, ' ', apellido) as nombre_completo FROM " . self::USERS_TABLE . " WHERE id = :id LIMIT 1");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC)['nombre_completo'] ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    // Obtener email por id 
    public function obtenerEmailPorId(int $id): ?string
    {
        try {
            $query = $this->db->prepare("SELECT email FROM " . self::USERS_TABLE . " WHERE id = :id LIMIT 1");
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC)['email'] ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}
