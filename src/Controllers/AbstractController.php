<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database;
use App\View;
use App\Request;
use App\Exceptions\ConfigurationException;

abstract class AbstractController
{
    protected const DEFAULT_ACTION = 'list';

    static private array $configuration = [];

    protected Request $request;
    protected View $view;
    protected Database $database;

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

    public function run(): void
    {
        /*---------------------------------------------------------------------
        Przypisanie wartości zwracanej metody action() do zmiennej $action
        oraz wywołanie jej. Jeśli wartość jest stringiem, to wywołanie zmiennej
        spowoduje wykonanie metody o nazwie przechowywanej wewnątrz zmiennej.
        ----------------------------------------------------------------------*/
        $action = $this->action() . 'Action';

        if (!method_exists($this, $action)) {
            $action = self::DEFAULT_ACTION . 'Action';
        }
        $this->$action();
    }

    protected function redirect(string $to, array $params): void
    {
        $location = $to;

        if (count($params)) {
            $queryParams = [];
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }

            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }

        header("Location: $location");
        exit;
    }

    /*---------------------------------
    Metoda określająca akcję kontrolera
    ----------------------------------*/
    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}
