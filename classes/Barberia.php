<?php

namespace App;

use PDO;
use PDOException;

class Barberia
{
    private $db;
    private const BARBERIA_TABLE = 'barberia';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerMembresia(): ?string
    {
        try {
            $query = $this->db->prepare("SELECT membresia FROM " . self::BARBERIA_TABLE);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC)['membresia'] ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function obtenerBarberia(): ?array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::BARBERIA_TABLE);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function actualizarBarberia(string $nombre, string $direccion, string $telefono, string $email, string $horario_apertura, string $horario_cierre, string $website): bool
    {
        try {
            $query = $this->db->prepare("UPDATE " . self::BARBERIA_TABLE . " SET nombre = :nombre, direccion = :direccion, telefono = :telefono, email = :email, horario_apertura = :horario_apertura, horario_cierre = :horario_cierre, website = :website");
            $query->execute(['nombre' => $nombre, 'direccion' => $direccion, 'telefono' => $telefono, 'email' => $email, 'horario_apertura' => $horario_apertura, 'horario_cierre' => $horario_cierre, 'website' => $website]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
