<?php

declare(strict_types=1);

namespace App;

use App\Exceptions\ConfigurationException;
use App\Exceptions\StorageException;
use App\Exceptions\NotFoundException;
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

    public function getNote(int $id): array
    {
        try {
            $query = "SELECT * FROM notes WHERE id = $id";
            $result = $this->conn->query($query);
            $note = $result->fetch(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            dump($th);
            throw new StorageException('Nie udało się pobrać szczegółów notatki', 400, $th);
        }

        if (!$note) {
            throw new NotFoundException("Notatka o id: $id nie istnieje");
            exit('Nie ma takiej notatki');
        }

        return $note;
    }


    public function searchNoteByDate(
        string $date,
        string $sortBy,
        string $sortOrder,
        int $pageNumber,
        int $pageSize
    ): array {
        try {

            $limit = $pageSize;
            $offset = ($pageNumber - 1) * $pageSize;

            if (!in_array($sortBy, ['created', 'title'])) {
                $sortBy = 'title';
            }

            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            $date = $this->conn->quote($date . '%',  PDO::PARAM_STR);

            $query = "
            SELECT id, title, created 
            FROM notes
            WHERE created LIKE ($date)
            ORDER BY $sortBy $sortOrder
            LIMIT $offset, $limit
            ";

            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się wyszukać notatek', 400, $th);
        }
    }


    public function searchNotes(
        string $phrase,
        string $sortBy,
        string $sortOrder,
        int $pageNumber,
        int $pageSize
    ): array {
        try {

            $limit = $pageSize;
            $offset = ($pageNumber - 1) * $pageSize;

            if (!in_array($sortBy, ['created', 'title'])) {
                $sortBy = 'title';
            }

            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);

            $query = "
            SELECT id, title, created 
            FROM notes
            WHERE title LIKE ($phrase)
            ORDER BY $sortBy $sortOrder
            LIMIT $offset, $limit
            ";

            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się wyszukać notatek', 400, $th);
        }
    }


    public function getSearchCount(string $phrase): int
    {
        try {
            $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
            $query = "SELECT count(*) AS cn FROM notes WHERE title LIKE ($phrase)";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new StorageException('Błąd przy próbie pobrania ilości notatek', 400);
            }

            return (int) $result['cn'];
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się pobrać informacji o liczbie notatek', 400, $th);
        }
    }

    public function getSearchCountByDate(string $date): int
    {
        try {
            $date = $this->conn->quote($date . '%', PDO::PARAM_STR);
            $query = "SELECT count(*) AS cn FROM notes WHERE created LIKE ($date)";
            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new StorageException('Błąd przy próbie pobrania ilości notatek', 400);
            }

            return (int) $result['cn'];
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się pobrać informacji o liczbie notatek', 400, $th);
        }
    }


    public function getNotes(string $sortBy, string $sortOrder, int $pageNumber, int $pageSize): array
    {
        try {

            $limit = $pageSize;
            $offset = ($pageNumber - 1) * $pageSize;

            if (!in_array($sortBy, ['created', 'title'])) {
                $sortBy = 'title';
            }

            if (!in_array($sortOrder, ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }
            $query = "
            SELECT id, title, created 
            FROM notes
            ORDER BY $sortBy $sortOrder
            LIMIT $offset, $limit
            ";

            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się pobrać danych o notatkach', 400, $th);
        }
    }

    public function getCount(): int
    {
        try {
            $query = "SELECT count(*) AS cn FROM notes";

            $result = $this->conn->query($query);
            $result = $result->fetch(PDO::FETCH_ASSOC);
            if ($result === false) {
                throw new StorageException('Błąd przy próbie pobrania ilości notatek', 400);
            }

            return (int) $result['cn'];
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się pobrać informacji o liczbie notatek', 400, $th);
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

    public function editNote(int $id, array $data)
    {
        try {
            $title = $this->conn->quote($data['title']);
            $description = $this->conn->quote($data['description']);

            $query = "
                UPDATE notes
                SET title = $title, description = $description
                WHERE id = $id
            ";

            $this->conn->exec($query);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się zaktualizować notatki', 400, $th);
        }
    }

    public function deleteNote(int $id): void
    {
        try {
            $query = "DELETE FROM notes WHERE id = $id LIMIT 1";
            $this->conn->exec($query);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się usunąć notatki', 400, $th);
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
