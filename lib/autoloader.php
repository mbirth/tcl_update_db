<?php

set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());

function simpleAutoloader($class)
{
    require_once __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
}

// PHP 7.0's default autoloader seems to have problems with PSR-4
spl_autoload_register('simpleAutoloader');
