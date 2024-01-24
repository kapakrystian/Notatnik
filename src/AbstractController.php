<?php

declare(strict_types=1);

namespace App;

require_once('src/Database.php');
require_once('src/View.php');
require_once('src/Exceptions/ConfigurationException.php');

use App\Exception\ConfigurationException;

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

    /*---------------------------------
    Metoda określająca akcję kontrolera
    ----------------------------------*/
    private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}
