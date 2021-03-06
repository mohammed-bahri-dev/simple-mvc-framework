<?php
if (file_exists('vendor/autoload.php')) {
    require 'vendor/autoload.php';
} else {
    echo "<h1>Please install via composer.json</h1>";
    echo "<p>Install Composer instructions: <a href='https://getcomposer.org/doc/00-intro.md#globally'>https://getcomposer.org/doc/00-intro.md#globally</a></p>";
    echo "<p>Once composer is installed navigate to the working directory in your terminal/command promt and enter 'composer install'</p>";
    exit;
}

if (!is_readable('app/Core/Config.php')) {
    die('No Config.php found, configure and rename Config.example.php to Config.php in app/Core.');
}

/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
    define('ENVIRONMENT', 'development');
    define ('ROOT', realpath(dirname(__FILE__)) . "/");
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but production will hide them.
 */

if (defined('ENVIRONMENT')) {
    switch (ENVIRONMENT) {
        case 'development':
            error_reporting(E_ALL);
            break;
        case 'production':
            error_reporting(0);
            break;
        default:
            exit('The application environment is not set correctly.');
    }

}

//initiate config
new Core\Config();

//create alias for Router
use Core\Router;

//define routes
Router::any('', 'Controllers\WelcomeController@index');
Router::any('login', 'Controllers\LoginController@mireLogin');
Router::any('callback', 'Controllers\LoginController@mireCallback');
Router::any('logout', 'Controllers\LoginController@mireLogout');
Router::any('auth', 'Controllers\LoginController@mireAuth');
Router::any('token', 'Controllers\LoginController@auc9Token');
Router::any('refresh', 'Controllers\LoginController@auc9Refresh');
Router::any('revoke', 'Controllers\LoginController@auc9Revoke');
Router::any('places', 'Controllers\LoginController@places');
Router::any('member', 'Controllers\LoginController@member');
Router::any('error', 'Controllers\LoginController@error');

//if no route found
Router::error('Core\Error@index');

//turn on old style routing
Router::$fallback = false;

//execute matched routes
Router::dispatch();
