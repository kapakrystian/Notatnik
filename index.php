<?php

declare(strict_types=1);

namespace App;

use App\Exception\AppException;
use App\Exception\ConfigurationException;
use Throwable;

require_once('src/Utils/debug.php');
require_once('src/Controller.php');

$configuration = require_once("config/config.php");

$request = [
    'get' => $_GET,
    'post' => $_POST
];

try {
    Controller::initConfiguration($configuration);
    // $controller = new Controller($request);
    // $controller->run();
    (new Controller($request))->run();
} catch (ConfigurationException $e) {
    // mail('kapakrystian@gmail.com', 'Error', $e->getMessage());
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    echo '<h5>Problem z aplikacją, proszę spróbować za chwilę.</h5>';
} catch (AppException $e) {
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    echo '<h5>' . $e->getMessage() . '</h5>';
} catch (Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    dump($e);
}
