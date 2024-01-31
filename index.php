<?php

declare(strict_types=1);

spl_autoload_register(function (string $classNamespace) {
    $path = str_replace(['\\', 'App/'], ['/', ''], $classNamespace);
    $path = "src/$path.php";
    require_once($path);
});

require_once('src/Utils/debug.php');
$configuration = require_once("config/config.php");

use App\Controllers\AbstractController;
use App\Controllers\NoteController;
use App\Request;
use App\Exceptions\AppException;
use App\Exceptions\ConfigurationException;

//dump($_SERVER);
$request = new Request($_GET, $_POST, $_SERVER);

try {
    AbstractController::initConfiguration($configuration);
    // $controller = new Controller($request);
    // $controller->run();
    (new NoteController($request))->run();
} catch (ConfigurationException $e) {
    // mail('kapakrystian@gmail.com', 'Error', $e->getMessage());
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    echo '<h5>Problem z aplikacją, proszę spróbować za chwilę.</h5>';
} catch (AppException $e) {
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    echo '<h5>' . $e->getMessage() . '</h5>';
} catch (\Throwable $e) {
    echo '<h3>Wystąpił błąd w aplikacji</h3>';
    dump($e);
}
