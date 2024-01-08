<?php

declare(strict_types=1);

namespace App;

require_once('src/Utils/debug.php');
require_once('src/View.php');

const DEFAULT_ACTION = 'list';

$action = $_GET['action'] ?? DEFAULT_ACTION;

$view = new View();

$viewParams = [];


switch ($action) {

    case 'create':
        $page = 'create';
        /*-----------------------------------------------------
        Flaga określająca moment przed i po utworzeniu notatki
        false -> przed, pobrany zostaje GET'em sam form
        true -> po, wysłane POST'em dane z form'a
        ------------------------------------------------------*/
        $created = false;

        if (!empty($_POST)) {
            $created = true;
            $viewParams = [
                'title' => $_POST['title'],
                'description' => $_POST['description']
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

$view->render($page, $viewParams);
