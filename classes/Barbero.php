<?php

namespace App;

use PDO;
use PDOException;
use InvalidArgumentException;

class Barbero
{
    private $db;
    private const TABLE_NAME = 'barberos';
    private const COLUMNS = ['barbero_id', 'nombre', 'apellido', 'celular', 'email'];

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function obtenerBarbero(string $campo, int $valor): ?array
    {
        if (!in_array($campo, self::COLUMNS)) {
            throw new InvalidArgumentException("Campo inválido: $campo");
        }

        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE $campo = :valor");
            $query->bindParam(':valor', $valor, PDO::PARAM_INT);
            $query->execute();
            return $query->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function obtenerTodosLosBarberos(): array
    {
        try {
            $query = $this->db->prepare("SELECT " . implode(', ', self::COLUMNS) . " FROM " . self::TABLE_NAME);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function obtenerHorariosDisponibles(string $fecha, int $duracionServicio, int $barberoId): array
    {
        try {
            $diasSemana = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            $diaNumero = date('N', strtotime($fecha));
            $diaSemana = strtolower($diasSemana[$diaNumero - 1]);

            $query = $this->db->prepare("SELECT horario_inicio, horario_fin FROM horarios_barberos WHERE barbero_id = :barberoId AND dia = :dia");
            $query->bindParam(':barberoId', $barberoId, PDO::PARAM_INT);
            $query->bindParam(':dia', $diaSemana, PDO::PARAM_STR);
            $query->execute();
            $horarios = $query->fetchAll(PDO::FETCH_ASSOC);

            if (empty($horarios)) {
                return [];
            }

            $reserva = new Reserva();
            $reservas = $reserva->obtenerReservasDelDia($fecha, $barberoId);

            $horariosDisponibles = [];

            foreach ($horarios as $horario) {
                $horaInicio = strtotime($horario['horario_inicio']);
                $horaFin = strtotime($horario['horario_fin']);

                for ($hora = $horaInicio; ($hora + ($duracionServicio * 60)) <= $horaFin; $hora += 15 * 60) {
                    $horaServicioInicio = $hora;
                    $horaServicioFin = $hora + ($duracionServicio * 60);

                    $ocupado = false;
                    foreach ($reservas as $reservaExistente) {
                        $horaInicioReserva = strtotime($reservaExistente['hora_inicio']);
                        $horaFinReserva = strtotime($reservaExistente['hora_fin']);

                        if (
                            ($horaServicioInicio < $horaFinReserva) &&
                            ($horaServicioFin > $horaInicioReserva)
                        ) {
                            $ocupado = true;
                            break;
                        }
                    }

                    if (!$ocupado) {
                        $horariosDisponibles[] = date('H:i', $horaServicioInicio);
                    }
                }
            }

            $horariosDisponibles = array_unique($horariosDisponibles);
            sort($horariosDisponibles);

            return array_values($horariosDisponibles);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function crearBarbero(string $nombre, string $apellido, string $celular, string $email): bool
    {
        try {
            $query = $this->db->prepare("INSERT INTO " . self::TABLE_NAME . " (nombre, apellido, celular, email) VALUES (:nombre, :apellido, :celular, :email)");
            $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $query->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $query->bindParam(':celular', $celular, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $result = $query->execute();

            if ($result) {
                $auth = new Auth();
                return $auth->crearUsuario($nombre, $apellido, $email);
            }

            return false;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function actualizarBarbero(int $id, string $nombre, string $apellido, string $celular, string $email): bool
    {
        try {
            $query = $this->db->prepare("UPDATE " . self::TABLE_NAME . " SET nombre = :nombre, apellido = :apellido, celular = :celular, email = :email WHERE barbero_id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':nombre', $nombre, PDO::PARAM_STR);
            $query->bindParam(':apellido', $apellido, PDO::PARAM_STR);
            $query->bindParam(':celular', $celular, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            return $query->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function eliminarBarbero(int $id): bool
    {
        try {
            $query = $this->db->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE barbero_id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function obtenerHorarios(int $barberoId): array
    {
        try {
            $query = $this->db->prepare("SELECT dia, horario_inicio, horario_fin FROM horarios_barberos WHERE barbero_id = :barbero_id");
            $query->bindParam(':barbero_id', $barberoId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            $horarios = [];
            foreach ($result as $row) {
                $horarios[$row['dia']] = $row;
            }
            return $horarios;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /*(isset($_POST['horarios'])) {
        $horarios = filter_input(INPUT_POST, 'horarios', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        // Update the barber's schedule
        $result = $barbero->actualizarHorarios($barberoId, $horarios); */

    public function actualizarHorarios(int $barberoId, array $horarios): bool
    {
        try {
            $this->db->beginTransaction();

            // Eliminar horarios existentes del barbero
            $query = $this->db->prepare("DELETE FROM horarios_barberos WHERE barbero_id = :barbero_id");
            $query->bindParam(':barbero_id', $barberoId, PDO::PARAM_INT);
            $query->execute();

            // Preparar la consulta para insertar nuevos horarios
            $query = $this->db->prepare("INSERT INTO horarios_barberos (barbero_id, dia, horario_inicio, horario_fin) VALUES (:barbero_id, :dia, :horario_inicio, :horario_fin)");

            foreach ($horarios as $dia => $horario) {
                $horarioInicio = $horario['inicio'];
                $horarioFin = $horario['fin'];

                // Vincular parámetros dentro del bucle
                $query->bindParam(':barbero_id', $barberoId, PDO::PARAM_INT);
                $query->bindParam(':dia', $dia, PDO::PARAM_STR);
                $query->bindParam(':horario_inicio', $horarioInicio, PDO::PARAM_STR);
                $query->bindParam(':horario_fin', $horarioFin, PDO::PARAM_STR);

                $query->execute();
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    // obtenerIdBarberoPorUsuarioId
    public function obtenerIdBarberoPorEmail(string $email): ?int
    {
        try {
            $query = $this->db->prepare("SELECT barbero_id FROM " . self::TABLE_NAME . " WHERE email = :email");
            $query->bindParam(':email', $email, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return $result['barbero_id'] ?? null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // obtenerNombrePorId
    public function obtenerNombreCompleto(int $barberoId): ?string
    {
        try {
            $query = $this->db->prepare("SELECT nombre, apellido FROM " . self::TABLE_NAME . " WHERE barbero_id = :barbero_id");
            $query->bindParam(':barbero_id', $barberoId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result['nombre'] . ' ' . $result['apellido'];
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
