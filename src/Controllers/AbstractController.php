<?php

declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Request;
use App\Exceptions\ConfigurationException;
use App\Exceptions\NotFoundException;
use App\Exceptions\StorageException;
use App\Model\NoteModel;

abstract class AbstractController
{
    protected const DEFAULT_ACTION = 'list';

    static private array $configuration = [];

    protected Request $request;
    protected View $view;
    protected NoteModel $noteModel;

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
        $this->noteModel = new NoteModel(self::$configuration['db']);

        $this->request = $request;
        $this->view = new View();
    }

    final public function run(): void
    {
        /*---------------------------------------------------------------------
        Przypisanie wartości zwracanej metody action() do zmiennej $action
        oraz wywołanie jej. Jeśli wartość jest stringiem, to wywołanie zmiennej
        spowoduje wykonanie metody o nazwie przechowywanej wewnątrz zmiennej.
        ----------------------------------------------------------------------*/
        try {
            $action = $this->action() . 'Action';

            if (!method_exists($this, $action)) {
                $action = self::DEFAULT_ACTION . 'Action';
            }
            $this->$action();
        } catch (StorageException $e) {
            $this->view->render(
                'error',
                ['message' => $e->getMessage()]
            );
        } catch (NotFoundException $e) {
            $this->redirect('/', ['error' => 'noteNotFound']);
        }
    }

    final protected function redirect(string $to, array $params): void
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
