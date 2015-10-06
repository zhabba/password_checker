<?php
use Main\Main;

chdir(dirname(__DIR__));
require_once "vendor/autoload.php";

try {
    $mainApp = new Main('config/checker.ini');
    $mainApp->run();
} catch (\Exception $e) {
    echo $e->getMessage();
}