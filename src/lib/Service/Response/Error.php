<?php

namespace Rk\Service\Response;

class Error extends Response
{
    /**
     * Shortcut for response errors
     * @param $data
     * @param int $code
     */
    public function __construct($data, $code = 500)
    {
        if (is_string($data)) {
            $data = array('message' => $data);
        }

        parent::__construct($data, $code);
    }
}