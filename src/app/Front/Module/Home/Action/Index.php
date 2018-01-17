<?php

namespace Rk\Application\Front\Module\Home\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;

class Index extends AbstractAction
{
    public function execute(): Response
    {
        return new Response('Default page');
    }

}