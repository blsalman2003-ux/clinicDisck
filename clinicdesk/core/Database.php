<?php
require_once __DIR__ . '/../config/database.php';

class Database
{

    private static ?Database $instance = null;

    private mysqli $conn;

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_errno) {

            error_log('DB connect error: ' . $this->conn->connect_error);
            throw new RuntimeException('Database connection failed. Please try again later.');
        }

        $this->conn->set_charset(DB_CHARSET);
    }

    private function __clone() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = []): mixed
    {
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log('Prepare failed: ' . $this->conn->error . ' | SQL: ' . $sql);
            return false;
        }

        if ($types !== '' && count($params) > 0) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            error_log('Execute failed: ' . $stmt->error . ' | SQL: ' . $sql);
            $stmt->close();
            return false;
        }

        $result = $stmt->get_result();
        $stmt->close();

        return ($result !== false) ? $result : true;
    }

    public function lastInsertId(): int
    {
        return (int) $this->conn->insert_id;
    }

    public function affectedRows(): int
    {
        return (int) $this->conn->affected_rows;
    }
}
