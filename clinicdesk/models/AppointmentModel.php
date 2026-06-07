<?php
require_once __DIR__ . '/BaseModel.php';

class AppointmentModel extends BaseModel
{


    public function book(array $data): bool
    {
        $result = $this->execute(
            'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, reason)
             VALUES (?, ?, ?, ?, ?)',
            'iisss',
            [
                (int) $data['patient_id'],
                (int) $data['doctor_id'],
                $data['appt_date'],
                $data['appt_time'],
                $data['reason'] ?? null,
            ]
        );
        return $result !== false;
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        $result = $this->execute(
            "SELECT id FROM appointments
             WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?
               AND status != 'cancelled'
             LIMIT 1",
            'iss', [$doctorId, $date, $time]
        );
        return (bool) $this->fetchOne($result);
    }


    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT a.*,
                    p.name  AS patient_name,  p.email AS patient_email,
                    p.phone AS patient_phone,
                    u.name  AS doctor_name,
                    s.name  AS specialization_name,
                    d.consultation_fee
             FROM appointments a
             JOIN users   p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users   u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.id = ?
             LIMIT 1',
            'i', [$id]
        );
        return $this->fetchOne($result);
    }


    private function baseSelect(): string
    {
        return 'SELECT a.*,
                       p.name  AS patient_name,
                       p.email AS patient_email,
                       p.phone AS patient_phone,
                       u.name  AS doctor_name,
                       s.name  AS specialization_name,
                       d.consultation_fee
                FROM appointments a
                JOIN users   p ON p.id = a.patient_id
                JOIN doctors d ON d.id = a.doctor_id
                JOIN users   u ON u.id = d.user_id
                JOIN specializations s ON s.id = d.specialization_id';
    }

    private function buildFilters(array $filters): array
    {
        $conditions = [];
        $types      = '';
        $params     = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'a.status = ?';
            $types       .= 's';
            $params[]     = $filters['status'];
        }

        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $types       .= 'i';
            $params[]     = (int) $filters['doctor_id'];
        }

        if (!empty($filters['patient_name'])) {
            $conditions[] = 'p.name LIKE ?';
            $types       .= 's';
            $params[]     = '%' . $filters['patient_name'] . '%';
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types       .= 's';
            $params[]     = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types       .= 's';
            $params[]     = $filters['end_date'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return compact('where', 'types', 'params');
    }

    public function getByPatient(int $patientId, int $offset, int $perPage, array $filters = []): array
    {
        $filters['_patient_id'] = $patientId;
        $f = $this->buildFilters($filters);

        $patientWhere = $f['where']
            ? $f['where'] . ' AND a.patient_id = ?'
            : 'WHERE a.patient_id = ?';

        $result = $this->execute(
            $this->baseSelect() . " $patientWhere ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?",
            $f['types'] . 'iii',
            array_merge($f['params'], [$patientId, $perPage, $offset])
        );
        return $this->fetchAll($result);
    }

    public function getByDoctor(int $doctorId, int $offset, int $perPage, array $filters = []): array
    {
        $f = $this->buildFilters($filters);

        $doctorWhere = $f['where']
            ? $f['where'] . ' AND a.doctor_id = ?'
            : 'WHERE a.doctor_id = ?';

        $result = $this->execute(
            $this->baseSelect() . " $doctorWhere ORDER BY a.appt_date ASC, a.appt_time ASC LIMIT ? OFFSET ?",
            $f['types'] . 'iii',
            array_merge($f['params'], [$doctorId, $perPage, $offset])
        );
        return $this->fetchAll($result);
    }

    public function getAll(int $offset, int $perPage, array $filters = []): array
    {
        $f = $this->buildFilters($filters);

        $result = $this->execute(
            $this->baseSelect() . " {$f['where']} ORDER BY a.created_at DESC LIMIT ? OFFSET ?",
            $f['types'] . 'ii',
            array_merge($f['params'], [$perPage, $offset])
        );
        return $this->fetchAll($result);
    }

    public function countFiltered(string $scope, int $scopeId, array $filters = []): int
    {
        $f = $this->buildFilters($filters);

        $scopeCondition = match($scope) {
            'patient' => 'a.patient_id = ?',
            'doctor'  => 'a.doctor_id = ?',
            default   => null,
        };

        if ($scopeCondition) {
            $where  = $f['where'] ? $f['where'] . " AND $scopeCondition" : "WHERE $scopeCondition";
            $types  = $f['types'] . 'i';
            $params = array_merge($f['params'], [$scopeId]);
        } else {
            $where  = $f['where'];
            $types  = $f['types'];
            $params = $f['params'];
        }

        $result = $this->execute(
            "SELECT COUNT(*) as total
             FROM appointments a
             JOIN users   p ON p.id = a.patient_id
             JOIN doctors d ON d.id = a.doctor_id
             JOIN users   u ON u.id = d.user_id
             $where",
            $types, $params
        );
        $row = $this->fetchOne($result);
        return (int) ($row['total'] ?? 0);
    }

    public function getTodayByDoctor(int $doctorId): array
    {
        $result = $this->execute(
            $this->baseSelect() . "
            WHERE a.doctor_id = ? AND a.appt_date = CURDATE()
              AND a.status != 'cancelled'
            ORDER BY a.appt_time ASC",
            'i', [$doctorId]
        );
        return $this->fetchAll($result);
    }

    public function getUpcomingByPatient(int $patientId, int $limit = 1): array
    {
        $result = $this->execute(
            $this->baseSelect() . "
            WHERE a.patient_id = ?
              AND a.appt_date >= CURDATE()
              AND a.status IN ('pending','confirmed')
            ORDER BY a.appt_date ASC, a.appt_time ASC
            LIMIT ?",
            'ii', [$patientId, $limit]
        );
        return $this->fetchAll($result);
    }

    public function getRecent(int $limit = 5): array
    {
        $result = $this->execute(
            $this->baseSelect() . ' ORDER BY a.created_at DESC LIMIT ?',
            'i', [$limit]
        );
        return $this->fetchAll($result);
    }


    public function countToday(): int
    {
        $result = $this->execute(
            "SELECT COUNT(*) as total FROM appointments WHERE appt_date = CURDATE()"
        );
        $row = $this->fetchOne($result);
        return (int) ($row['total'] ?? 0);
    }

    public function countThisWeekByStatus(): array
    {
        $result = $this->execute(
            'SELECT status, COUNT(*) as total
             FROM appointments
             WHERE WEEK(appt_date) = WEEK(NOW()) AND YEAR(appt_date) = YEAR(NOW())
             GROUP BY status'
        );
        return $this->fetchAll($result);
    }

    public function countMonthStatsByDoctor(int $doctorId): array
    {
        $result = $this->execute(
            "SELECT
                COUNT(*) as total,
                SUM(status = 'pending')   as pending,
                SUM(status = 'completed') as completed
             FROM appointments
             WHERE doctor_id = ?
               AND MONTH(appt_date) = MONTH(NOW())
               AND YEAR(appt_date)  = YEAR(NOW())",
            'i', [$doctorId]
        );
        return $this->fetchOne($result) ?? ['total' => 0, 'pending' => 0, 'completed' => 0];
    }

    public function countStatsByPatient(int $patientId): array
    {
        $result = $this->execute(
            "SELECT
                SUM(status IN ('pending','confirmed')) as active,
                SUM(status = 'completed')              as completed
             FROM appointments
             WHERE patient_id = ?",
            'i', [$patientId]
        );
        return $this->fetchOne($result) ?? ['active' => 0, 'completed' => 0];
    }

    public function countPerDay(int $days = 14): array
    {
        $result = $this->execute(
            'SELECT appt_date, COUNT(*) as total
             FROM appointments
             WHERE appt_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             GROUP BY appt_date
             ORDER BY appt_date ASC',
            'i', [$days]
        );
        return $this->fetchAll($result);
    }


    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        if ($notes !== '') {
            $result = $this->execute(
                'UPDATE appointments SET status = ?, doctor_notes = ? WHERE id = ?',
                'ssi', [$status, $notes, $id]
            );
        } else {
            $result = $this->execute(
                'UPDATE appointments SET status = ? WHERE id = ?',
                'si', [$status, $id]
            );
        }
        return $result !== false;
    }

    public function cancel(int $id, int $cancelledBy, string $reason): bool
    {
        $result = $this->execute(
            "UPDATE appointments
             SET status = 'cancelled', cancelled_by = ?, cancellation_reason = ?
             WHERE id = ?",
            'isi', [$cancelledBy, $reason, $id]
        );
        return $result !== false;
    }

    public function logStatusChange(int $appointmentId, int $changedBy, string $oldStatus, string $newStatus, string $note = ''): bool
    {
        $result = $this->execute(
            'INSERT INTO appointment_logs
             (appointment_id, changed_by_user_id, old_status, new_status, note)
             VALUES (?, ?, ?, ?, ?)',
            'iisss', [$appointmentId, $changedBy, $oldStatus, $newStatus, $note]
        );
        return $result !== false;
    }
}
