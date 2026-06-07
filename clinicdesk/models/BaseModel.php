<?php
require_once __DIR__ . '/../core/Database.php';

abstract class BaseModel
{

    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute(string $sql, string $types = '', array $params = []): mixed
    {
        return $this->db->query($sql, $types, $params);
    }

    protected function fetchAll(mixed $result): array
    {
        if (!$result || $result === true) return [];
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    protected function fetchOne(mixed $result): ?array
    {
        if (!$result || $result === true) return null;
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    protected function lastInsertId(): int
    {
        return $this->db->lastInsertId();
    }
}
