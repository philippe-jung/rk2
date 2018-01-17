<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Response\Success;

class Collection extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_GET;

    public function execute(): Response
    {
        $query = '
            SELECT * 
            FROM job 
            WHERE status = "active"';

        $res = DB::getInstance()->select($query);
dump($res);
        return new Success('1');
    }
}