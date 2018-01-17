<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Response\Success;

class Create extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_GET;

    protected $requiredParams = array(
        'title'       => self::FORMAT_STRING,
        'category'    => self::FORMAT_STRING,
        'description' => self::FORMAT_STRING,
        'location'    => self::FORMAT_STRING,
    );

    public function execute(): Response
    {
        $query = '
            INSERT INTO job (title, category, location, created_at, updated_at, status)
            VALUES ()';

        $res = DB::getInstance()->insert($query);
dump($res);
        return new Success('1');
    }
}