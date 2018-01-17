<?php

namespace Rk\Action;


class Response
{
    /**
     * HTTP response code
     *
     * @var integer
     */
    protected $code;

    /**
     * Content to be output
     *
     * @var string
     */
    protected $content;

    public function __construct($content, $code = 200)
    {
        $this->content = $content;
        $this->code = $code;
    }

    /**
     * Send code + data to the client
     */
    public function send()
    {
        http_response_code($this->code);
        echo $this->content;
    }
}