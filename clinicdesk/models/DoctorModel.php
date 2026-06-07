<?php
require_once __DIR__ . '/BaseModel.php';

class DoctorModel extends BaseModel
{


    public function findByUserId(int $userId): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.user_id = ?
             LIMIT 1',
            'i', [$userId]
        );
        return $this->fetchOne($result);
    }

    public function findById(int $doctorId): ?array
    {
        $result = $this->execute(
            'SELECT d.*, u.name, u.email, u.phone, u.avatar, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE d.id = ?
             LIMIT 1',
            'i', [$doctorId]
        );
        return $this->fetchOne($result);
    }

    public function getAll(): array
    {
        $result = $this->execute(
            'SELECT d.id, d.available_days, d.consultation_fee,
                    u.name, s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             WHERE u.is_active = 1
             ORDER BY u.name ASC'
        );
        return $this->fetchAll($result);
    }

    public function getAllPaginated(int $offset, int $perPage): array
    {
        $result = $this->execute(
            'SELECT d.id, d.consultation_fee, d.available_days,
                    u.name, u.email, u.is_active,
                    s.name AS specialization_name
             FROM doctors d
             JOIN users u ON u.id = d.user_id
             JOIN specializations s ON s.id = d.specialization_id
             ORDER BY u.name ASC
             LIMIT ? OFFSET ?',
            'ii', [$perPage, $offset]
        );
        return $this->fetchAll($result);
    }

    public function countAll(): int
    {
        $result = $this->execute('SELECT COUNT(*) as total FROM doctors');
        $row    = $this->fetchOne($result);
        return (int) ($row['total'] ?? 0);
    }

    public function getAvailableDays(int $doctorId): array
    {
        $result = $this->execute(
            'SELECT available_days FROM doctors WHERE id = ? LIMIT 1',
            'i', [$doctorId]
        );
        $row = $this->fetchOne($result);
        if (!$row || empty($row['available_days'])) return [];
        return explode(',', $row['available_days']);
    }


    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days)
             VALUES (?, ?, ?, ?, ?)',
            'iisds',
            [
                (int) $data['user_id'],
                (int) $data['specialization_id'],
                $data['bio']              ?? null,
                (float) ($data['consultation_fee'] ?? 0),
                $data['available_days']   ?? 'Sun,Mon,Tue,Wed,Thu',
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool
    {
        $fields = [];
        $types  = '';
        $params = [];

        $allowed = [
            'specialization_id' => 'i',
            'bio'               => 's',
            'consultation_fee'  => 'd',
            'available_days'    => 's',
            'photo'             => 's',
        ];

        foreach ($allowed as $field => $type) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $types   .= $type;
                $params[] = $data[$field];
            }
        }

        if (empty($fields)) return false;

        $types   .= 'i';
        $params[] = $doctorId;

        $result = $this->execute(
            'UPDATE doctors SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $types, $params
        );
        return $result !== false;
    }
}
