<?php
require_once __DIR__ . '/BaseModel.php';

class SpecializationModel extends BaseModel
{

    public function getAll(): array
    {
        $result = $this->execute('SELECT * FROM specializations ORDER BY name ASC');
        return $this->fetchAll($result);
    }

    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM specializations WHERE id = ? LIMIT 1',
            'i', [$id]
        );
        return $this->fetchOne($result);
    }

    public function create(string $name): int
    {
        $this->execute(
            'INSERT INTO specializations (name) VALUES (?)',
            's', [$name]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, string $name): bool
    {
        $result = $this->execute(
            'UPDATE specializations SET name = ? WHERE id = ?',
            'si', [$name, $id]
        );
        return $result !== false;
    }

    public function delete(int $id): bool
    {
        $result = $this->execute(
            'DELETE FROM specializations WHERE id = ?',
            'i', [$id]
        );
        return $result !== false;
    }

    public function isSafeToDelete(int $id): bool
    {
        $result = $this->execute(
            'SELECT COUNT(*) as total FROM doctors WHERE specialization_id = ?',
            'i', [$id]
        );
        $row = $this->fetchOne($result);
        return ((int) ($row['total'] ?? 1)) === 0;
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $result = $this->execute(
                'SELECT id FROM specializations WHERE name = ? AND id != ? LIMIT 1',
                'si', [$name, $excludeId]
            );
        } else {
            $result = $this->execute(
                'SELECT id FROM specializations WHERE name = ? LIMIT 1',
                's', [$name]
            );
        }
        return (bool) $this->fetchOne($result);
    }
}
