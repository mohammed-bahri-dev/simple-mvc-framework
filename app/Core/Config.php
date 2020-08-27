<?php
namespace Core;

use Helpers\Session;
use Dotenv\Dotenv;

/*
 * config - an example for setting up system settings
 * When you are done editing, rename this file to 'config.php'
 *
 * @author David Carr - dave@daveismyname.com - http://daveismyname.com
 * @author Edwin Hoksberg - info@edwinhoksberg.nl
 * @version 2.2
 * @date June 27, 2014
 * @date updated May 18 2015
 */
class Config
{
    public function __construct()
    {
        /*
        * Load Environement Variables
        */
        $dotEnv = Dotenv::createImmutable(ROOT);
        $dotEnv->load();

        //turn on output buffering
        ob_start();

        //site address
        define('DIR', getenv('DIR'));
        define('CAPI_NOM_APP', getenv('CAPI_NOM_APP'));
        define('CAPI_VERSION_APP', getenv('CAPI_VERSION_APP'));

        // authent collab
        define('CAPI_CLE_CONSOMMATEUR', getenv('CAPI_CLE_CONSOMMATEUR'));
        define('CAPI_SECRET_CONSOMMATEUR', getenv('CAPI_SECRET_CONSOMMATEUR'));
        define('CAPI_MIRE_URL', getenv('CAPI_MIRE_URL'));
        define('CAPI_MIRE_CALLBACK_URL', getenv('CAPI_MIRE_CALLBACK_URL'));
        define('CAPI_AUC9_URL', getenv('CAPI_AUC9_URL'));

        // places
        define('CAPI_PLACES_URL', getenv('CAPI_PLACES_URL'));

        //set default controller and method for legacy calls
        define('DEFAULT_CONTROLLER', 'welcome');
        define('DEFAULT_METHOD', 'index');

        //set the default template
        define('TEMPLATE', 'default');

        //set a default language
        define('LANGUAGE_CODE', 'fr');

        //database details ONLY NEEDED IF USING A DATABASE
        define('DB_TYPE', 'mysql');
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'dbname');
        define('DB_USER', 'root');
        define('DB_PASS', 'password');
        define('PREFIX', 'smvc_');

        //set prefix for sessions
        define('SESSION_PREFIX', 'smvc_');

        //optionall create a constant for the name of the site
        define('SITETITLE', 'V2.2');

        //optionall set a site email address
        //define('SITEEMAIL', '');

        //turn on custom error handling
        set_exception_handler('Core\Logger::ExceptionHandler');
        set_error_handler('Core\Logger::ErrorHandler');

        //set timezone
        date_default_timezone_set('Europe/Paris');

        //start sessions
        Session::init();
    }
}
