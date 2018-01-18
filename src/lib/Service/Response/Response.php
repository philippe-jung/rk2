<?php

namespace Rk\Service\Response;

class Response extends \Rk\Action\Response
{
    /**
     * Send code + data to the client
     */
    public function send()
    {
        http_response_code($this->code);
        header('Content-Type: application/json');
        echo json_encode($this->content);
    }
}