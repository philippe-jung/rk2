<?php

require_once(__DIR__ . '/bootstrap.php');

try {

    // simple logic to call the correct app dispatcher based on the URL
    $component = \Rk\Routing\Router::getActionFromRequest('Front');
    $response = $component->execute();

    $response->send();

} catch (Throwable $e) {
    // generic error management
    http_response_code(500);
    echo '<pre>Sorry, an error has occurred.</pre>';

    if (\Rk\Config::isDebug()) {
        if (!empty($e->xdebug_message)) {
            echo '<table>' . $e->xdebug_message . '</table>';
        } else {
            var_dump($e);
        }
    }
}
