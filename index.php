<?php
// устраняем проблему с кодировкой
header('Content-type: text/html; charset=utf-8');

require_once 'vendor/autoload.php';

use Core\App;

App::getInstance()->run();


