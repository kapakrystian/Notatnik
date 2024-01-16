<?php

declare(strict_types=1);

namespace App;

use App\Exception\ConfigurationException;

require_once('src/Database.php');
require_once('src/View.php');
require_once('src/Exceptions/ConfigurationException.php');

class Controller
{
    private const DEFAULT_ACTION = 'list';

    static private array $configuration = [];

    private array $request;
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
    public function __construct(array $request)
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
        $viewParams = [];

        switch ($this->action()) {

            case 'create':
                $page = 'create';
                /*-----------------------------------------------------
                Flaga określająca moment przed i po utworzeniu notatki
                false -> przed, pobrany zostaje GET'em sam form
                true -> po, wysłane POST'em dane z form'a
                ------------------------------------------------------*/

                $data = $this->getRequestPost();
                if (!empty($data)) {

                    /*-------------------------------------------------
                    Nie ma potrzeby przekazywania całego POST'a dlatego
                    dane przekazujemy zmienną $noteData
                    --------------------------------------------------*/
                    $noteData = [
                        'title' => $data['title'],
                        'description' => $data['description']
                    ];

                    $this->database->createNote($noteData);
                    header('Location: /?before=created');
                }

                break;

            case 'show':
                # code...

            default:
                $page = 'list';

                $data = $this->getRequestGet();

                $viewParams['before'] = $data['before'] ?? null;

                break;
        }

        $this->view->render($page, $viewParams);
    }

    /*---------------------------------
    Metoda określająca akcję kontrolera
    ----------------------------------*/
    private function action(): string
    {
        $data = $this->getRequestGet();
        return $data['action'] ?? self::DEFAULT_ACTION;
    }

    /*----------------------------
    Metoda zwracająca dane z GET'a
    -----------------------------*/
    private function getRequestGet(): array
    {
        return $this->request['get'] ?? [];
    }

    /*-----------------------------
    Metoda zwracająca dane z POST'a
    ------------------------------*/
    private function getRequestPost(): array
    {
        return $this->request['post'] ?? [];
    }
}
