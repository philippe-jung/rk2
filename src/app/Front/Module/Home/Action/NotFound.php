<?php

namespace Rk\Application\Front\Module\Home\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;

class NotFound extends AbstractAction
{
    public function execute(): Response
    {
        return new Response('Page not found', 404);
    }

}