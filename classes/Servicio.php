<?php

namespace App;

use PDO;
use PDOException;
use InvalidArgumentException;

class Servicio
{
    private $db;
    private const TABLE_NAME = 'servicios';
    private const COLUMNS = ['id', 'nombre', 'descripcion', 'duracion', 'precio'];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerServicioPorId(int $id): ?array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function obtenerServicio(string $campo, $valor): ?array
    {
        if (!in_array($campo, self::COLUMNS)) {
            throw new InvalidArgumentException('Campo no permitido.');
        }

        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE $campo = :valor");
            $query->bindParam(':valor', $valor);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function obtenerDuracionPorId(int $id): ?int
    {
        try {
            $servicio = $this->obtenerServicioPorId($id);
            return $servicio ? (int)$servicio['duracion'] : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function ListarServicios(): array
    {
        try {
            $query = $this->db->prepare("SELECT id, nombre, descripcion, duracion, precio, estado FROM servicios");
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerServiciosTotales(): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME);
            $query->execute();
            return $query->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    // obtener precio por id
    public function obtenerPrecioPorId(int $id): ?float
    {
        try {
            $servicio = $this->obtenerServicioPorId($id);
            return $servicio ? (float)$servicio['precio'] : null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function actualizarServicio(int $id, string $nombre, string $descripcion, int $duracion, float $precio, string $estado): bool
    {
        try {
            $query = $this->db->prepare("UPDATE " . self::TABLE_NAME . " SET nombre = :nombre, descripcion = :descripcion, duracion = :duracion, precio = :precio, estado = :estado WHERE id = :id");
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':descripcion', $descripcion);
            $query->bindParam(':duracion', $duracion);
            $query->bindParam(':precio', $precio);
            $query->bindParam(':estado', $estado);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // eliminarServicio($servicioId);
    public function eliminarServicio(int $id): bool
    {
        try {
            $query = $this->db->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            return $query->rowCount() > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // $servicioId = $servicio->crearServicio($nombre, $descripcion, $duracion, $precio);
    public function crearServicio(string $nombre, string $descripcion, int $duracion, float $precio): int
    {
        try {
            $query = $this->db->prepare("INSERT INTO " . self::TABLE_NAME . " (nombre, descripcion, duracion, precio) VALUES (:nombre, :descripcion, :duracion, :precio)");
            $query->bindParam(':nombre', $nombre);
            $query->bindParam(':descripcion', $descripcion);
            $query->bindParam(':duracion', $duracion);
            $query->bindParam(':precio', $precio);
            $query->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }
}
