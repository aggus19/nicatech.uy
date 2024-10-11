<?php

namespace App;

use PDO;
use Exception;
use InvalidArgumentException;

class Pago
{
    private $db;
    private const TABLE_NAME = 'pagos'; // Columnas: id, reserva_id, cliente_id, monto, fecha_pago

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todas las reservas.
     *
     * @return array
     */
    public function obtenerTodosLosPagos(): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas.');
        }
    }

    // Registrar un pago
    public function registrarPago(int $reservaId, int $clienteId, float $monto): bool
    {
        try {
            $query = $this->db->prepare("INSERT INTO " . self::TABLE_NAME . " (reserva_id, cliente_id, monto, fecha_pago) VALUES (:reserva_id, :cliente_id, :monto, NOW())");
            $query->bindParam(':reserva_id', $reservaId, PDO::PARAM_INT);
            $query->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $query->bindParam(':monto', $monto, PDO::PARAM_STR);
            return $query->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al registrar el pago.');
        }
    }

    // obtenerPagoPorReservaId que devuelva true or false
    public function obtenerPagoPorReservaId(int $reservaId): bool
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE reserva_id = :reserva_id");
            $query->bindParam(':reserva_id', $reservaId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ? true : false;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el pago.');
        }
    }
}
