<?php

namespace App;

use PDO;
use PDOException;
use Exception;
use InvalidArgumentException;
use DateTime;
use IntlDateFormatter;

class Reserva
{
    private $db;
    private const TABLE_NAME = 'reservas';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Obtener todas las reservas.
     *
     * @return array
     */
    public function obtenerTodasLasReservas(): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME);
            $query->execute();
            $reservas = $query->fetchAll(PDO::FETCH_ASSOC);

            // Establecer la configuración regional en español
            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y');
            $formatterHora = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'H:mm');

            foreach ($reservas as &$reserva) {
                // Formatear las fechas
                $fechaReservada = new DateTime($reserva['fecha_reservada']);
                $horaInicio = new DateTime($reserva['hora_inicio']);

                // Crear nuevo campo para la fecha en formato de texto
                $reserva['fecha_reservada_texto'] = $formatter->format($fechaReservada);
                $reserva['hora_inicio_texto'] = $formatterHora->format($horaInicio);

                // Mantener la fecha_reservada original
                $reserva['fecha_reservada'] = $fechaReservada->format('Y-m-d');

                // Formatear la hora_inicio en H:i:s
                $reserva['hora_inicio'] = $horaInicio->format('H:i:s');
            }

            return $reservas;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas.');
        }
    }
    /**
     * Obtener una reserva por su ID.
     *
     * @param int $id
     * @return array|false
     */
    public function obtenerReservaPorId(int $id)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $reserva = $query->fetch(PDO::FETCH_ASSOC);

            if ($reserva) {
                // Establecer la configuración regional en español
                $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');
                $formatterEmision = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');

                // Formatear las fechas
                $fechaReservada = new DateTime($reserva['fecha_reservada'] . ' ' . $reserva['hora_inicio']);
                $fechaEmision = new DateTime($reserva['fecha_emision']);

                // Crear nuevo campo para la fecha en formato de texto
                $reserva['fecha_reservada_texto'] = $formatter->format($fechaReservada);
                $reserva['fecha_emision'] = $formatterEmision->format($fechaEmision);

                // Mantener la fecha_reservada original
                $reserva['fecha_reservada'] = (new DateTime($reserva['fecha_reservada']))->format('Y-m-d');

                // Formatear la hora_inicio en H:i:s y H:i
                $horaInicio = new DateTime($reserva['hora_inicio']);
                $reserva['hora_inicio'] = $horaInicio->format('H:i:s');
                $reserva['hora_inicio_texto'] = $horaInicio->format('H:i');
            }

            return $reserva;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reserva.');
        }
    }

    public function crearReserva(string $fecha, string $hora_inicio, int $duracion, int $cliente_id, int $barbero_id, int $servicio_id): int
    {
        // Validar formato de fecha
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new InvalidArgumentException('Formato de fecha inválido. Debe ser YYYY-MM-DD.');
        }

        try {
            $query = $this->db->prepare("INSERT INTO " . self::TABLE_NAME . " (fecha_reservada, hora_inicio, duracion, cliente_id, barbero_id, servicio_id) VALUES (:fecha_reservada, :hora_inicio, :duracion, :cliente_id, :barbero_id, :servicio_id)");
            $query->bindParam(':fecha', $fecha);
            $query->bindParam(':hora_inicio', $hora_inicio);
            $query->bindParam(':duracion', $duracion, PDO::PARAM_INT);
            $query->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $query->bindParam(':barbero_id', $barbero_id, PDO::PARAM_INT);
            $query->bindParam(':servicio_id', $servicio_id, PDO::PARAM_INT);
            $query->execute();
            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al crear reserva.');
        }
    }

    public function obtenerCambioPorcentualReservas(): float
    {
        $hoy = date('Y-m-d');
        $ayer = date('Y-m-d', strtotime('-1 day'));
        try {
            $queryHoy = $this->db->prepare("SELECT COUNT(*) as count FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :hoy");
            $queryHoy->bindParam(':hoy', $hoy);
            $queryHoy->execute();
            $reservasHoy = (int) $queryHoy->fetch(PDO::FETCH_ASSOC)['count'];

            $queryAyer = $this->db->prepare("SELECT COUNT(*) as count FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :ayer");
            $queryAyer->bindParam(':ayer', $ayer);
            $queryAyer->execute();
            $reservasAyer = (int) $queryAyer->fetch(PDO::FETCH_ASSOC)['count'];

            if ($reservasAyer == 0) {
                return $reservasHoy > 0 ? 100.0 : 0.0;
            }

            return (($reservasHoy - $reservasAyer) / $reservasAyer) * 100;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener cambio porcentual de reservas.');
        }
    }

    public function contadorTotalReservas(): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME);
            $query->execute();
            return $query->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el total de reservas.');
        }
    }

    // obtener reserva por campo: cancelada, confirmada, o pendiente
    public function obtenerReservasPorCampo(string $campo, string $valor): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE $campo = :valor ORDER BY fecha_reservada DESC");
            $query->bindParam(':valor', $valor);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas por campo.');
        }
    }

    // Obtener las reservas del dia de hoy pendientes y ordenarlas por hora de inicio reciente
    public function obtenerReservasPendientesDeHoy(): array
    {
        $hoy = date('Y-m-d');
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :hoy AND estado = 'pendiente' ORDER BY hora_inicio ASC");
            $query->bindParam(':hoy', $hoy);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas pendientes de hoy.');
        }
    }

    // Contador de reservas por campo: cancelada, confirmada, o pendiente
    public function contadorReservasPorCampo(string $campo, string $valor): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE $campo = :valor");
            $query->bindParam(':valor', $valor);
            $query->execute();
            return $query->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el total de reservas por campo.');
        }
    }

    // Actualizar una reserva
    public function actualizarReserva(int $id, int $clienteId, int $servicioId, string $fecha, string $hora, string $estado, int $barberoId, string $metodoPago): bool
    {
        try {
            // Obtener la duración del servicio
            $queryDuracion = $this->db->prepare("SELECT duracion FROM servicios WHERE id = :servicioId");
            $queryDuracion->bindParam(':servicioId', $servicioId, PDO::PARAM_INT);
            $queryDuracion->execute();
            $duracion = $queryDuracion->fetchColumn();

            if ($duracion === false) {
                throw new Exception('Error al obtener la duración del servicio.');
            }

            // Actualizar la reserva con la duración obtenida
            $query = $this->db->prepare("
                UPDATE " . self::TABLE_NAME . " 
                SET cliente_id = :clienteId, servicio_id = :servicioId, fecha_reservada = :fecha, hora_inicio = :hora, estado = :estado, duracion = :duracion, barbero_id = :barberoId, metodo_pago = :metodoPago
                WHERE id = :id
            ");
            $query->bindParam(':clienteId', $clienteId, PDO::PARAM_INT);
            $query->bindParam(':servicioId', $servicioId, PDO::PARAM_INT);
            $query->bindParam(':fecha', $fecha);
            $query->bindParam(':hora', $hora);
            $query->bindParam(':estado', $estado);
            $query->bindParam(':duracion', $duracion, PDO::PARAM_INT);
            $query->bindParam(':barberoId', $barberoId, PDO::PARAM_INT);
            $query->bindParam(':metodoPago', $metodoPago);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            return $query->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al actualizar reserva.');
        }
    }

    // Eliminar una reserva
    public function eliminarReserva(int $id): bool
    {
        try {
            $query = $this->db->prepare("DELETE FROM " . self::TABLE_NAME . " WHERE id = :id");
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            return $query->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al eliminar reserva.');
        }
    }

    // Obtener las ultimas 5 reservas id, cliente_id, fecha, hora_inicio, metodo_pago, servicio_id, precio(de servicio_id), estado
    public function obtenerUltimas5ReservasPorUsuario(int $usuarioId): array
    {
        try {
            $query = $this->db->prepare("
                SELECT 
                    r.id, 
                    CONCAT(c.nombre, ' ', c.apellido) AS nombre_cliente, 
                    DATE_FORMAT(CONCAT(r.fecha_reservada, ' ', r.hora_inicio), '%d/%m/%Y %H:%i') AS fecha, 
                    r.metodo_pago, 
                    s.nombre AS nombre_servicio, 
                    s.precio, 
                    r.estado,
                    CONCAT(b.nombre, ' ', b.apellido) AS nombre_barbero
                FROM " . self::TABLE_NAME . " r 
                JOIN clientes c ON r.cliente_id = c.cliente_id 
                JOIN servicios s ON r.servicio_id = s.id 
                JOIN barberos b ON r.barbero_id = b.barbero_id
                WHERE r.cliente_id = :usuarioId
                ORDER BY r.fecha_reservada DESC 
                LIMIT 5
            ");
            $query->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener las reservas del usuario.');
        }
    }

    // Obtener las reservas del día de hoy
    public function obtenerReservasDelDiaDeHoy(): array
    {
        $hoy = date('Y-m-d');
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :hoy");
            $query->bindParam(':hoy', $hoy);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas del día de hoy.');
        }
    }

    // Contador de total de reservas del mes actual.
    public function contadorTotalReservasDelMes(): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE MONTH(fecha_reservada) = MONTH(CURRENT_DATE()) AND YEAR(fecha_reservada) = YEAR(CURRENT_DATE())");
            $query->execute();
            return $query->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el total de reservas del mes.');
        }
    }

    // Devolver el porcentaje de cambio de reservas del mes actual comparado con el mes anterior. Asignarle el signo de + o - dependiendo del resultado.
    public function calcularPorcentajeRespectoAlMesAnterior(): array
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM " . self::TABLE_NAME . " WHERE MONTH(fecha_reservada) = MONTH(CURRENT_DATE()) - 1 AND YEAR(fecha_reservada) = YEAR(CURRENT_DATE())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $reservasMesAnterior = $result['total'] ?? 0;

            $reservasMesActual = $this->contadorTotalReservasDelMes();

            if ($reservasMesAnterior == 0) {
                $porcentaje = $reservasMesActual > 0 ? '+100.00%' : '0.00%';
                $estado = $reservasMesActual > 0 ? 'success' : 'danger';
                return ['porcentaje' => $porcentaje, 'estado' => $estado];
            }

            $porcentaje = (($reservasMesActual - $reservasMesAnterior) / $reservasMesAnterior) * 100;
            $signo = $porcentaje >= 0 ? '+' : '';
            $estado = $porcentaje >= 0 ? 'success' : 'danger';
            return ['porcentaje' => $signo . number_format($porcentaje, 2) . '%', 'estado' => $estado];
        } catch (Exception $e) {
            error_log($e->getMessage());
            return ['porcentaje' => 'Error', 'estado' => 'danger'];
        }
    }
    // Dependiendo del servicio id que mas se repite en las reservas, devolver el nombre del servicio y la cantidad de veces que se repite. Es para una estadistica de los servicios mas solicitados o popular.
    public function servicioMasSolicitado(): array
    {
        try {
            $query = $this->db->prepare("
                SELECT 
                    s.nombre AS nombre, 
                    COUNT(r.servicio_id) AS cantidad 
                FROM " . self::TABLE_NAME . " r 
                JOIN servicios s ON r.servicio_id = s.id 
                GROUP BY r.servicio_id 
                ORDER BY cantidad DESC 
                LIMIT 1
            ");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            // Verificar si hay resultados y devolver un array con las claves necesarias
            if ($result) {
                return $result;
            } else {
                return [
                    'nombre' => 'N/A',
                    'cantidad' => 0
                ];
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el servicio más solicitado.');
        }
    }

    // Contador de reservas realizadas por un cliente en particular.
    public function contadorReservasPorCliente(int $clienteId): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE cliente_id = :cliente_id");
            $query->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchColumn();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener el total de reservas por cliente.');
        }
    }

    /* Se usa para el Calendario */
    public function obtenerReservasPorRango($start, $end)
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE fecha_reservada BETWEEN :start AND :end");
            $query->bindParam(':start', $start);
            $query->bindParam(':end', $end);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas por rango.');
        }
    }

    public function obtenerReservasDelDia(string $dia, int $barbero_id): array
    {
        $this->validarFecha($dia);
        return $this->ejecutarConsulta(
            "SELECT hora_inicio, duracion FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :dia AND barbero_id = :barbero_id",
            [':dia' => $dia, ':barbero_id' => $barbero_id]
        );
    }

    /**
     * Obtener horarios disponibles para un barbero en un día seleccionado.
     *
     * @param string $diaSeleccionado
     * @param int $duracionServicio
     * @param int $barbero_id
     * @return array
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function obtenerHorariosDisponibles(string $diaSeleccionado, int $duracionServicio, int $barbero_id): array
    {
        $this->validarFecha($diaSeleccionado);

        // Obtener horarios predefinidos
        $horariosPredefinidos = $this->obtenerHorariosPredefinidos($barbero_id);
        $intervalos = $this->generarIntervalos($horariosPredefinidos['horario_inicio'], $horariosPredefinidos['horario_fin'], 15);
        $horariosOcupados = $this->procesarReservas($this->obtenerReservasDelDia($diaSeleccionado, $barbero_id));

        // Filtrar horarios disponibles
        $horariosDisponibles = array_values(array_filter($intervalos, function ($hora) use ($duracionServicio, $horariosOcupados) {
            return !$this->esSolapado($hora, $duracionServicio, $horariosOcupados);
        }));

        // Formatear los horarios disponibles a 'H:i' para la vista
        return array_map(function ($hora) {
            return date('H:i', strtotime($hora));
        }, $horariosDisponibles);
    }
    private function ejecutarConsulta(string $sql, array $params = [], bool $isInsert = false)
    {
        try {
            $query = $this->db->prepare($sql);
            foreach ($params as $key => &$val) {
                $query->bindParam($key, $val);
            }
            $query->execute();

            return $isInsert ? (int)$this->db->lastInsertId() : $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error en la consulta.');
        }
    }

    private function validarFecha(string $fecha): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            throw new InvalidArgumentException('Formato de fecha inválido. Debe ser YYYY-MM-DD.');
        }
    }

    private function generarIntervalos(string $inicio, string $fin, int $intervalo): array
    {
        $intervalos = [];
        $horaActual = strtotime($inicio);
        $horaFin = strtotime($fin);

        while ($horaActual < $horaFin) {
            $intervalos[] = date('H:i:s', $horaActual);
            $horaActual = strtotime("+$intervalo minutes", $horaActual);
        }

        return $intervalos;
    }

    private function obtenerHorariosPredefinidos(int $barbero_id): array
    {
        return $this->ejecutarConsulta(
            "SELECT horario_inicio, horario_fin FROM horarios_barberos WHERE barbero_id = :barbero_id",
            [':barbero_id' => $barbero_id]
        )[0]; // Devuelve solo el primer resultado
    }

    private function procesarReservas(array $reservasDelDia): array
    {
        return array_map(function ($reserva) {
            $horaFin = strtotime("+{$reserva['duracion']} minutes", strtotime($reserva['hora_inicio']));
            return [
                'inicio' => $reserva['hora_inicio'],
                'fin' => date('H:i', $horaFin)
            ];
        }, $reservasDelDia);
    }

    private function esSolapado(string $hora, int $duracionServicio, array $horariosOcupados): bool
    {
        $horaFinPropuesta = strtotime("+$duracionServicio minutes", strtotime($hora));

        foreach ($horariosOcupados as $ocupado) {
            $horaInicioOcupado = strtotime($ocupado['inicio']);
            $horaFinOcupado = strtotime($ocupado['fin']);

            if (
                ($horaFinPropuesta > $horaInicioOcupado && $horaFinPropuesta <= strtotime('+5 minutes', $horaInicioOcupado)) ||
                (strtotime($hora) >= $horaInicioOcupado && strtotime($hora) < $horaFinOcupado)
            ) {
                return true;
            }
        }

        return false;
    }

    // obtenerContadorReservasPorCliente
    public function obtenerContadorReservasPorCliente(int $clienteId): int
    {
        try {
            $query = $this->db->prepare("SELECT COUNT(*) FROM " . self::TABLE_NAME . " WHERE cliente_id = :cliente_id");
            $query->bindParam(':cliente_id', $clienteId, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function marcarComoPagada(int $id): bool
    {
        $query = $this->db->prepare("UPDATE " . self::TABLE_NAME . " SET pagada = 'Sí', fecha_pago = NOW() WHERE id = :id");
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        return $query->execute();
    }

    public function obtenerReservasParaCalendario(): array
    {
        try {
            $query = $this->db->prepare("SELECT id, fecha_reservada, hora_inicio, duracion, cliente_id, barbero_id, servicio_id, estado FROM " . self::TABLE_NAME);
            $query->execute();
            $reservas = $query->fetchAll(PDO::FETCH_ASSOC);

            $cliente = new Cliente();
            $eventos = [];
            foreach ($reservas as $reserva) {
                $inicio = new DateTime($reserva['fecha_reservada'] . ' ' . $reserva['hora_inicio']);
                $fin = clone $inicio;
                $fin->modify('+' . $reserva['duracion'] . ' minutes');

                $clienteData = $cliente->obtenerClientePorId($reserva['cliente_id']);
                $nombreCompleto = $clienteData['nombre'] . ' ' . $clienteData['apellido'];

                $eventos[] = [
                    'title' => 'Reserva ' . $nombreCompleto,
                    'start' => $inicio->format('Y-m-d\TH:i:s'),
                    'end' => $fin->format('Y-m-d\TH:i:s'),
                    'description' => 'Cliente ID: ' . $reserva['cliente_id'] . ', Barbero ID: ' . $reserva['barbero_id'] . ', Servicio ID: ' . $reserva['servicio_id'] . ', Estado: ' . $reserva['estado'],
                    'url' => 'ver-reserva?id=' . $reserva['id']
                ];
            }

            return $eventos;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw new Exception('Error al obtener reservas para el calendario.');
        }
    }

    // ListarReservasPorEstado
    public function listarReservasPorEstado(string $estado): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE estado = :estado");
            $query->bindParam(':estado', $estado);
            $query->execute();
            $reservas = $query->fetchAll(PDO::FETCH_ASSOC);

            // Establecer la configuración regional en español
            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');
            $formatterEmision = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');

            foreach ($reservas as &$reserva) {
                // Formatear las fechas
                $fechaReservada = new DateTime($reserva['fecha_reservada'] . ' ' . $reserva['hora_inicio']);
                $fechaEmision = new DateTime($reserva['fecha_emision']);

                // Crear nuevo campo para la fecha en formato de texto
                $reserva['fecha_reservada_texto'] = $formatter->format($fechaReservada);
                $reserva['fecha_emision'] = $formatterEmision->format($fechaEmision);

                // Mantener la fecha_reservada original
                $reserva['fecha_reservada'] = (new DateTime($reserva['fecha_reservada']))->format('Y-m-d');

                // Formatear la hora_inicio en H:i:s y H:i
                $horaInicio = new DateTime($reserva['hora_inicio']);
                $reserva['hora_inicio'] = $horaInicio->format('H:i:s');
                $reserva['hora_inicio_texto'] = $horaInicio->format('H:i');
            }

            return $reservas;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    //ListarReservasPorFecha
    public function listarReservasPorFecha(string $fecha): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :fecha");
            $query->bindParam(':fecha', $fecha);
            $query->execute();
            $reservas = $query->fetchAll(PDO::FETCH_ASSOC);

            // Establecer la configuración regional en español
            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');
            $formatterEmision = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');

            foreach ($reservas as &$reserva) {
                // Formatear las fechas
                $fechaReservada = new DateTime($reserva['fecha_reservada'] . ' ' . $reserva['hora_inicio']);
                $fechaEmision = new DateTime($reserva['fecha_emision']);

                // Crear nuevo campo para la fecha en formato de texto
                $reserva['fecha_reservada_texto'] = $formatter->format($fechaReservada);
                $reserva['fecha_emision'] = $formatterEmision->format($fechaEmision);

                // Mantener la fecha_reservada original
                $reserva['fecha_reservada'] = (new DateTime($reserva['fecha_reservada']))->format('Y-m-d');

                // Formatear la hora_inicio en H:i:s y H:i
                $horaInicio = new DateTime($reserva['hora_inicio']);
                $reserva['hora_inicio'] = $horaInicio->format('H:i:s');
                $reserva['hora_inicio_texto'] = $horaInicio->format('H:i');
            }

            return $reservas;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    //listarReservasPorFechaYEstado($fecha, $estado);
    public function listarReservasPorFechaYEstado(string $fecha, string $estado): array
    {
        try {
            $query = $this->db->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE fecha_reservada = :fecha AND estado = :estado");
            $query->bindParam(':fecha', $fecha);
            $query->bindParam(':estado', $estado);
            $query->execute();
            $reservas = $query->fetchAll(PDO::FETCH_ASSOC);

            // Establecer la configuración regional en español
            $formatter = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');
            $formatterEmision = new IntlDateFormatter('es_ES', IntlDateFormatter::FULL, IntlDateFormatter::FULL, 'Europe/Madrid', IntlDateFormatter::GREGORIAN, 'EEEE, d \'de\' MMMM \'de\' y, H:mm');

            foreach ($reservas as &$reserva) {
                // Formatear las fechas
                $fechaReservada = new DateTime($reserva['fecha_reservada'] . ' ' . $reserva['hora_inicio']);
                $fechaEmision = new DateTime($reserva['fecha_emision']);

                // Crear nuevo campo para la fecha en formato de texto
                $reserva['fecha_reservada_texto'] = $formatter->format($fechaReservada);
                $reserva['fecha_emision'] = $formatterEmision->format($fechaEmision);

                // Mantener la fecha_reservada original
                $reserva['fecha_reservada'] = (new DateTime($reserva['fecha_reservada']))->format('Y-m-d');

                // Formatear la hora_inicio en H:i:s y H:i
                $horaInicio = new DateTime($reserva['hora_inicio']);
                $reserva['hora_inicio'] = $horaInicio->format('H:i:s');
                $reserva['hora_inicio_texto'] = $horaInicio->format('H:i');
            }

            return $reservas;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
