<?php

use Rk\Routing\Router;

$GLOBALS['routing'] = array(
    'Front' => array(
        // default routes for front
        Router::ROUTE_DEFAULT  => ['Home', 'Index'],
        Router::ROUTE_NOTFOUND => ['Home', 'NotFound'],
    ),

    'Example' => array(
        // special routes for the services
//        'github/repos'  => ['Github', 'Repos'],
//        'github/users'  => 'Github/Users',
//        'distance/:user1/:user2'      => ['Distance', 'Get'],
//        'GET=distance/:user1/:user2'      => ['Distance', 'Get'],
//        'distance/:user1/:user2/test'      => ['Distance', 'Get'],
//        'distance/:user1/:user2'      => ['Distance', 'Get'],
        'distance'      => ['Distance', 'Get'],
    )
);