<?php

declare(strict_types=1);

namespace App;

require_once('src/View.php');

class Controller
{
    private const DEFAULT_ACTION = 'list';

    private array $request;
    private View $view;

    public function __construct(array $request)
    {
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
                $created = false;

                $data = $this->getRequestPost();
                if (!empty($data)) {
                    $created = true;
                    $viewParams = [
                        'title' => $data['title'],
                        'description' => $data['description']
                    ];
                }
                $viewParams['created'] = $created;
                break;

            case 'show':
                # code...

            default:
                $page = 'list';
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
