<?php
header('Access-Control-Allow-Origin: *');

//ini_set('session.gc_maxlifetime', 3600); // 1 hora
//ini_set('session.cookie_lifetime', 3600);

//session_set_cookie_params(3600); // 1 hora

//date_default_timezone_set('America/Sao_Paulo');
//date_default_timezone_set('America/Brasilia');
date_default_timezone_set('America/Fortaleza');

chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

$env = (isset($_SERVER['APPLICATION_ENV'])) ? $_SERVER['APPLICATION_ENV'] : 'production';

define('APPLICATION_ENV', $env);

define('BASE_PATCH', str_replace(DIRECTORY_SEPARATOR . 'public', '', __DIR__));

if (isset($_SERVER['HTTP_HOST'])) {
    define('BASE_URL', $_SERVER['HTTP_HOST']);
}

if (APPLICATION_ENV == 'development') {
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
    ini_set("display_errors", 1);
}


// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();

/// Função Debug
function debug($mixExpression, $boolExit = TRUE, $boolFinish = NULL)
{
    static $arrMessages;
    if (!$arrMessages) {
        $arrMessages = array();
    }

    if ($boolFinish) {
        return (implode(" <br/> ", $arrMessages));
    }

    $arrBacktrace = debug_backtrace();
    $strMessage = "";
    $strMessage .= "<fieldset><legend><font color=\"#007000\">debug</font></legend><pre>";
    foreach ($arrBacktrace[0] as $strAttribute => $mixValue) {
        if ($strAttribute == 'args') continue;
        $strMessage .= "<b>" . $strAttribute . "</b> " . $mixValue . "\n";
    }
    $strMessage .= "<hr />";

    # Abre o buffer, impedindo que seja impresso na tela alguma coisa
    ob_start();
    var_dump($mixExpression);
    # Pega todo o buffer
    $strMessage .= ob_get_clean();

    $strMessage .= "</pre></fieldset>";


    foreach ($arrMessages as $messages) {
        print $messages;
        ob_flush();
        flush();
    }
    print $strMessage;
    print "<br /><font color=\"#700000\" size=\"4\"><b>D I E</b></font>";

    if ($boolExit) {
        exit();
    }
}