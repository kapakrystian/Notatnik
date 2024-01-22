<?php

declare(strict_types=1);

namespace App;

use App\Request;
use App\Exception\ConfigurationException;
use App\Exception\NotFoundException;

require_once('src/Database.php');
require_once('src/View.php');
require_once('src/Exceptions/ConfigurationException.php');

class Controller
{
    private const DEFAULT_ACTION = 'list';

    static private array $configuration = [];

    private Request $request;
    private View $view;
    private Database $database;

    /*-----------------------------------------
    Statyk pobierający konfigurację bazy danych
    ------------------------------------------*/
    static public function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }

    /*---------
    Konstruktor
    -----------*/
    public function __construct(Request $request)
    {
        if (empty(self::$configuration['db'])) {
            throw new ConfigurationException('Configuration Error');
        }
        $this->database = new Database(self::$configuration['db']);

        $this->request = $request;
        $this->view = new View();
    }

    /*-----------------------------
    Metoda z "routingiem" aplikacji
    ------------------------------*/
    public function run(): void
    {
        switch ($this->action()) {

            case 'create':
                $page = 'create';

                if ($this->request->hasPost()) {

                    /*-------------------------------------------------
                    Nie ma potrzeby przekazywania całego POST'a dlatego
                    dane przekazujemy zmienną $noteData
                    --------------------------------------------------*/
                    $noteData = [
                        'title' => $this->request->postParam('title'),
                        'description' => $this->request->postParam('description')
                    ];

                    $this->database->createNote($noteData);
                    header('Location: /?before=created');
                    exit;
                }

                break;

            case 'show':
                $page = 'show';

                // $data = $this->getRequestGet();
                // $noteId = (int)($data['id'] ?? null);

                $noteId = (int)$this->request->getParam('id');

                if (!$noteId) {
                    header('Location: /?error=missingNoteId');
                    exit;
                }

                try {
                    $note = $this->database->getNote($noteId);
                } catch (NotFoundException $e) {
                    header('Location: /?error=noteNotFound');
                    exit;
                }

                $viewParams = [
                    'note' => $note
                ];

                break;

            default:
                $page = 'list';

                $viewParams = [
                    'notes' => $this->database->getNotes(),
                    'before' => $this->request->getParam('before'),
                    'error' => $this->request->getParam('error')
                ];

                break;
        }

        $this->view->render($page, $viewParams ?? []);
    }

    /*---------------------------------
    Metoda określająca akcję kontrolera
    ----------------------------------*/
    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}
