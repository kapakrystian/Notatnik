<?php

declare(strict_types=1);

namespace App;

require_once('Exceptions/AppException.php');
require_once('Exceptions/StorageException.php');
require_once('Exceptions/ConfigurationException.php');

use App\Exception\ConfigurationException;
use App\Exception\StorageException;
use PDO;
use PDOException;
use Throwable;

class Database
{
    private PDO $conn;

    public function __construct(array $config)
    {
        try {
            $this->validateConfig($config);
            $this->createConnection($config);
        } catch (PDOException $e) {
            throw new StorageException('Connection Error');
        }
    }

    public function getNotes(): array
    {
        try {
            $query = "SELECT id, title, created FROM notes";
            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $th);
        }
    }

    public function createNote(array $data): void
    {
        try {

            $title = $this->conn->quote($data['title']);
            $description = $this->conn->quote($data['description']);
            $created = $this->conn->quote(date('Y-m-d H:i:s'));

            $query = "INSERT INTO notes(title, description, created)
                     VALUES($title, $description, $created)";

            $this->conn->exec($query);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się utworzyć nowej notatki', 400, $th);
            dump($th);
            exit;
        }
    }

    private function createConnection(array $config): void
    {
        $dsn = "mysql:dbname={$config['database']};host={$config['host']}";

        $this->conn = new PDO(
            $dsn,
            $config['user'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
    }

    private function validateConfig(array $config): void
    {
        if (
            empty($config['database'])
            || empty($config['host'])
            || empty($config['user'])
            || empty($config['password'])
        ) {
            throw new ConfigurationException('Storage configuration error');
        }
    }
}
