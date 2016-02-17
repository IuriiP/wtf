<?php
/**
 * Use Composer's autoloader
 * 
 * @var \Composer\Autoload\ClassLoader Autoloader
 */
$loader = require_once '../vendor/autoload.php';

/**
 * Use Dotenv environment
 * 
 * @var \Dotenv\Dotenv Environtment
 */
$dotenv = new Dotenv\Dotenv(__DIR__.DIRECTORY_SEPARATOR.'..');
// $dotenv->load(); // for append .env to environment
$dotenv->overload(); // for override environment by .env

print_r($loader);
print_r($dotenv);

phpinfo();
