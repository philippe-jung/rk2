<?php


namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Request;

class CollectionDispatcher extends AbstractAction
{
    public function execute(): Response
    {
        switch (Request::getMethod()) {
            case Request::METHOD_GET:
                $redirect = 'Collection';
            break;

            case Request::METHOD_POST:
                $redirect = 'Create';
            break;
        }
        $class = 'Rk\Application\RecruitMe\Module\Job\Action\\' . $redirect;

        $action = new $class();
    }
}