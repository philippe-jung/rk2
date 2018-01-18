<?php

use Rk\Routing\Router;
use Rk\Request;

$GLOBALS['routing'] = array(
    'Front' => array(
        // default routes for front
        Router::ROUTE_DEFAULT  => ['Home', 'Index'],
        Router::ROUTE_NOTFOUND => ['Home', 'NotFound'],
    ),

    'ExampleApi' => array(
        'distance' => ['Distance', 'Get'],
    ),

    'RecruitMe' => array(
        'job'     => array(
            Request::METHOD_GET  => ['Job', 'Collection'],
            Request::METHOD_POST => ['Job', 'Create'],
        ),
        'job/:id' => array(
            Request::METHOD_GET    => ['Job', 'Retrieve'],
            Request::METHOD_PUT    => ['Job', 'Update'],
            Request::METHOD_DELETE => ['Job', 'Delete'],
        ),
    ),
);