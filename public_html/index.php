<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

#
define('REQUEST_MICROTIME', microtime(true));

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
define('PUBLIC_DIRECTORY', __DIR__);

define('APP_PRODUCTION', 'production');
define('APP_DEVELOPMENT', 'development');
define('APP_TESTING', 'testing');

define('APP_ENV', getenv('APP_ENV') ?: APP_PRODUCTION);

/**
 * This makes our life easier when dealing with paths.
 * Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

define('CORE_DIRECTORY', getcwd());

// Decline static file requests back to the PHP built-in webserver
if(php_sapi_name() === 'cli-server')
{
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if(__FILE__ !== $path && is_file($path))
    {
        return false;
    }
    
    unset($path);
}

// Setup autoloading
require 'vendor/autoload.php';

// Config
$appConfig = include 'config/application.config.php';
if(file_exists('config/development.config.php'))
{
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include 'config/development.config.php');
}

// Run the application!
Zend\Mvc\Application::init($appConfig)->run();
