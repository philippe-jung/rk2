<?php

$GLOBALS['debug'] = true;

if (!empty($GLOBALS['debug'])) {
    ini_set('display_errors', true);
    // when using the display errors directive, PHP will use a HTTP 200 Code, even in case of errors
    // therefor we use a custom shutdown function to force it to 500 and yet have the error displayed
    // Note: this is only needed for the errors that are not extending the Error class of PHP7
    register_shutdown_function('shutdownHandler');
}

function shutdownHandler()
{
    $error = error_get_last();
    if ($error['type'] == \E_COMPILE_ERROR ) {
        http_response_code(500);
    }
}

// autoload everything thanks to Composer
require_once(__DIR__ . '/../../vendor/autoload.php');

// load the config files
require_once(__DIR__ . '/../conf/config.php');
require_once(__DIR__ . '/../conf/routing.php');
