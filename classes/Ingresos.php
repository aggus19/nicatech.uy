<?php

namespace App;

use PDO;
use Exception;
use InvalidArgumentException;

class Ingresos
{
    private $db;
    private const TABLE_NAME = 'pagos';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getIngresos(): float
    {
        try {
            $sql = "SELECT SUM(monto) as total FROM " . self::TABLE_NAME . " WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE()) AND YEAR(fecha_pago) = YEAR(CURRENT_DATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] !== null ? (float)$result['total'] : 0.0;
        } catch (\Throwable $th) {
            throw new Exception("Error al obtener los ingresos: " . $th->getMessage());
        }
    }

    public function calcularPorcentajeRespectoAlMesAnterior(): array
    {
        try {
            $sql = "SELECT SUM(monto) as total FROM " . self::TABLE_NAME . " WHERE MONTH(fecha_pago) = MONTH(CURRENT_DATE()) - 1 AND YEAR(fecha_pago) = YEAR(CURRENT_DATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $ingresosMesAnterior = $result['total'] ?? 0.0;
            $ingresosMesActual = $this->getIngresos();

            if ($ingresosMesAnterior == 0) {
                $porcentaje = $ingresosMesActual > 0 ? '+100.00%' : '0.00%';
                $estado = $ingresosMesActual > 0 ? 'success' : 'danger';
                return ['porcentaje' => $porcentaje, 'estado' => $estado];
            }

            $porcentaje = (($ingresosMesActual - $ingresosMesAnterior) / $ingresosMesAnterior) * 100;
            $signo = $porcentaje >= 0 ? '+' : '';
            $estado = $porcentaje >= 0 ? 'success' : 'danger';
            return ['porcentaje' => $signo . number_format($porcentaje, 2) . '%', 'estado' => $estado];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['porcentaje' => 'Error', 'estado' => 'danger'];
        }
    }
}
