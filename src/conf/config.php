<?php

$GLOBALS['config'] = array(
    // endpoint to the Github mockup
    'github' => array(
        'root'     => getenv('BASE_URL') . '/exampleApi/',
    ),

    // fake data used by the Github mockup
    'mockup' => array(
        // ids of repos to which users have contributed
        'contribs' => array(
            'john'  => array(1, 2, 3),
            'jack'  => array(1, 2, 4),
            'jim'   => array(3),
            'dan'   => array(4),
            'eric'  => array(1),
            'elsa'  => array(3, 4),
            'boris' => array(5),
            'adam'  => array(),
        ),
        // "id => name" for each repo
        'repos'    => array(
            1 => 'automotive/4x4',
            2 => 'automotive/cycle',
            3 => 'screen/tv',
            4 => 'screen/phone',
            5 => 'screen/pc',
        )
    ),
);
