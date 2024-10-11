<?php

namespace App;

use PDO;
use PDOException;
use InvalidArgumentException;
use Exception;

class Cliente
{
    private $db;
    private const TABLE_NAME = 'futuros';
    private const COLUMNS = ['id', 'email', 'fecha_solicitud', 'ip'];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Guardar la solicitud de cuando se recibe un email que se anota en la lista de espera del programa.
    public function guardarEmail(string $email): int
    {
        // Verificar si el email ya existe
        $sql = "SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            throw new Exception('El email ya está registrado en la lista de espera.');
        }

        // Insertar el nuevo email
        $sql = "INSERT INTO " . self::TABLE_NAME . " (email, ip) VALUES (:email, :ip)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);

        // Obtener la IP pública
        $ip = file_get_contents('https://api.ipify.org');
        $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);

        try {
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Hubo un problema al intentar guardar el email.');
        }
    }
}
