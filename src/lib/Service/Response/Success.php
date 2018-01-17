<?php

namespace Rk\Service\Response;

class Success extends Response
{
    /**
     * Shortcut for success response
     * @param $data
     * @param int $code
     */
    public function __construct($data, $code = 200)
    {
        // for single string returns, wrap the data in a "message" index
        if (is_string($data)) {
            $data = array('message' => $data);
        }
        parent::__construct($data, $code);
    }
}