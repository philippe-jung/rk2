<?php

require_once(__DIR__ . '/bootstrap.php');

use Rk\Service\Response\Error;
use Rk\Exception;
use Rk\Service\Exception\Exception as ServiceException;

try {
    // simple logic to call the correct app dispatcher based on the URL
    // all requests coming to this file will be routed to the 'RecruitMe' application
    $component = \Rk\Routing\Router::getActionFromRequest('RecruitMe');
    $response = $component->execute();
    $response->send();

} catch (Exception\RouteNotFound $e) {
        // no route was found
        $response = new Error('No such endpoint');
        $response->send();
} catch (ServiceException $e) {
        // a public exception occurred
        $response = new Error($e->getMessage());
        $response->send();
} catch (\Throwable $e) {
    // error management for API calls
    if (\Rk\Config::isDebug()) {
        $response = new Error(array(
            'message' => 'An internal error has occurred',
            'debug' => array(
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            )
        ));
    } else {
        $response = new Error('An internal error has occurred');
    }
    $response->send();
}