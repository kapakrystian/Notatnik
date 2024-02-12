<?php

declare(strict_types=1);

namespace App\Model;

use App\Exceptions\StorageException;
use App\Exceptions\NotFoundException;
use PDO;
use Throwable;

class NoteModel extends AbstractModel implements ModelInterface
{

    /*---------------------------
    pobieranie szczegółów notatki
    ----------------------------*/

    public function get(int $id): array
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

    /*----------------------------------
    wyszukiwanie notatek po tytule/dacie
    -----------------------------------*/

    public function search(
        string $sortBy,
        string $sortOrder,
        int $pageNumber,
        int $pageSize,
        ?string $phrase,
        ?string $date
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
            $date = $this->conn->quote($date . '%',  PDO::PARAM_STR);


            switch (true) {
                case (!empty($phrase) && !empty($date)):
                    $query = "SELECT id, title, created FROM notes WHERE created LIKE ($date) AND title LIKE ($phrase) ORDER BY $sortBy $sortOrder LIMIT $offset, $limit";
                    break;
                case (empty($phrase) && !empty($date)):
                    $query = "SELECT id, title, created FROM notes WHERE created LIKE ($date) ORDER BY $sortBy $sortOrder LIMIT $offset, $limit";
                    break;
                case (!empty($phrase) && empty($date)):
                    $query = "SELECT id, title, created FROM notes WHERE title LIKE ($phrase) ORDER BY $sortBy $sortOrder LIMIT $offset, $limit";
                default:
                    $query = "SELECT id, title, created FROM notes ORDER BY $sortBy $sortOrder LIMIT $offset, $limit";
                    break;
            }

            $result = $this->conn->query($query);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się wyszukać notatek', 400, $th);
        }
    }

    /*------------------------------------------------------------
    metody zliczające ilość notatek/ ilość notatek po filtrowaniu
     - akcje powiązane z paginacją na stronie głównej
    ------------------------------------------------------------*/

    public function count(): int
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

    public function searchCount(?string $phrase = '', ?string $date = ''): int
    {
        try {
            $phrase = $this->conn->quote('%' . $phrase . '%', PDO::PARAM_STR);
            $date = $this->conn->quote($date . '%', PDO::PARAM_STR);

            switch (true) {
                case (!empty($phrase) && !empty($date)):
                    $query = "SELECT count(*) AS cn FROM notes WHERE title LIKE ($phrase) AND created LIKE ($date)";
                    break;
                case (!empty($phrase) && empty($date)):
                    $query = "SELECT count(*) AS cn FROM notes WHERE title LIKE ($phrase)";
                    break;
                case (empty($phrase) && !empty($date)):
                    $query = "SELECT count(*) AS cn FROM notes WHERE created LIKE ($date)";
                    break;
                default:
                    $query = "SELECT count(*) AS cn FROM notes";
                    break;
            }
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

    /*----------
    metody CRUD
    -----------*/

    public function create(array $data): void
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

    public function list(string $sortBy, string $sortOrder, int $pageNumber, int $pageSize): array
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

    public function edit(int $id, array $data): void
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

    public function delete(int $id): void
    {
        try {
            $query = "DELETE FROM notes WHERE id = $id LIMIT 1";
            $this->conn->exec($query);
        } catch (Throwable $th) {
            throw new StorageException('Nie udało się usunąć notatki', 400, $th);
        }
    }
}
