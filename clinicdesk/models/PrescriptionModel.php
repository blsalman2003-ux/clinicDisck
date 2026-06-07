<?php
require_once __DIR__ . '/BaseModel.php';

class PrescriptionModel extends BaseModel
{


    public function findByAppointmentId(int $apptId): ?array
    {
        $result = $this->execute(
            'SELECT * FROM prescriptions WHERE appointment_id = ? LIMIT 1',
            'i', [$apptId]
        );
        return $this->fetchOne($result);
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM prescriptions WHERE id = ? LIMIT 1',
            'i', [$id]
        );
        return $this->fetchOne($result);
    }

    public function getByPatient(int $patientId): array
    {
        $result = $this->execute(
            "SELECT pr.*,
                    a.appt_date, a.appt_time,
                    u.name AS doctor_name,
                    s.name AS specialization_name
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             JOIN doctors      d ON d.id = a.doctor_id
             JOIN users        u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.patient_id = ?
             ORDER BY pr.created_at DESC",
            'i', [$patientId]
        );
        return $this->fetchAll($result);
    }

    public function getByDoctor(int $doctorId): array
    {
        $result = $this->execute(
            "SELECT pr.*,
                    a.appt_date, a.appt_time,
                    p.name AS patient_name,
                    s.name AS specialization_name
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             JOIN users        p ON p.id = a.patient_id
             JOIN doctors      d ON d.id = a.doctor_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE a.doctor_id = ?
             ORDER BY pr.created_at DESC",
            'i', [$doctorId]
        );
        return $this->fetchAll($result);
    }

    public function countByPatient(int $patientId): int
    {
        $result = $this->execute(
            'SELECT COUNT(*) as total
             FROM prescriptions pr
             JOIN appointments a ON a.id = pr.appointment_id
             WHERE a.patient_id = ? AND pr.file_path IS NOT NULL',
            'i', [$patientId]
        );
        $row = $this->fetchOne($result);
        return (int) ($row['total'] ?? 0);
    }


    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path)
             VALUES (?, ?, ?, ?, ?)',
            'issss',
            [
                (int) $data['appointment_id'],
                $data['diagnosis'],
                $data['medications'],
                $data['notes']      ?? null,
                $data['file_path']  ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $types  = '';
        $params = [];

        $allowed = ['diagnosis', 'medications', 'notes', 'file_path'];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $types   .= 's';
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $types   .= 'i';
        $params[] = $id;

        $result = $this->execute(
            'UPDATE prescriptions SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $types, $params
        );
        return $result !== false;
    }
}
