<?php
require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{


    public function findById(int $id): ?array
    {
        $result = $this->execute(
            'SELECT * FROM users WHERE id = ? LIMIT 1',
            'i', [$id]
        );
        return $this->fetchOne($result);
    }

    public function findByEmail(string $email): ?array
    {
        $result = $this->execute(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            's', [$email]
        );
        return $this->fetchOne($result);
    }

    public function getAllPaginated(int $offset, int $perPage, string $role = '', string $search = ''): array
    {
        $conditions = [];
        $types      = '';
        $params     = [];

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $types .= 'ss';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $types .= 'ii';
        $params[] = $perPage;
        $params[] = $offset;

        $result = $this->execute(
            "SELECT id, name, email, role, phone, is_active, created_at
             FROM users $where
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            $types, $params
        );
        return $this->fetchAll($result);
    }

    public function countAll(string $role = '', string $search = ''): int
    {
        $conditions = [];
        $types      = '';
        $params     = [];

        if ($role !== '') {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $types .= 'ss';
            $like = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }

        $where  = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $result = $this->execute("SELECT COUNT(*) as total FROM users $where", $types, $params);
        $row    = $this->fetchOne($result);
        return (int) ($row['total'] ?? 0);
    }

    public function countByRole(): array
    {
        $result = $this->execute(
            'SELECT role, COUNT(*) as total FROM users GROUP BY role'
        );
        return $this->fetchAll($result);
    }


    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone)
             VALUES (?, ?, ?, ?, ?)',
            'sssss',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'],
                $data['phone'] ?? null,
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $types  = '';
        $params = [];

        $allowed = ['name', 'phone', 'avatar'];
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
            'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?',
            $types, $params
        );
        return $result !== false;
    }

    public function updatePassword(int $id, string $newHash): bool
    {
        $result = $this->execute(
            'UPDATE users SET password = ?, first_login = 0 WHERE id = ?',
            'si', [$newHash, $id]
        );
        return $result !== false;
    }

    public function toggleActive(int $id): bool
    {
        $result = $this->execute(
            'UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?',
            'i', [$id]
        );
        return $result !== false;
    }

    public function delete(int $id): bool
    {
        $result = $this->execute('DELETE FROM users WHERE id = ?', 'i', [$id]);
        return $result !== false;
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $result = $this->execute(
                'SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1',
                'si', [$email, $excludeId]
            );
        } else {
            $result = $this->execute(
                'SELECT id FROM users WHERE email = ? LIMIT 1',
                's', [$email]
            );
        }
        return (bool) $this->fetchOne($result);
    }
}
