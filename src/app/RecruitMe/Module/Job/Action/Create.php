<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Application\RecruitMe\Module\Job\Helper;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Response\Success;

class Create extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_POST;

    protected $requiredParams = array(
        'title'       => self::FORMAT_STRING,
        'category'    => self::FORMAT_STRING,
        'description' => self::FORMAT_STRING,
        'location'    => self::FORMAT_STRING,
    );

    /**
     * @return Response
     * @throws \Rk\DB\Exception
     * @throws \Rk\Service\Exception\Exception
     */
    public function execute(): Response
    {
        // insert the job
        $query = '
            INSERT INTO job (title, category, description, location)
            VALUES (:title, :category, :description, :location)';

        $insertId = DB::getInstance()->insert($query, array(
            'title' => $this->getValidatedParam('title'),
            'category' => $this->getValidatedParam('category'),
            'description' => $this->getValidatedParam('description'),
            'location' => $this->getValidatedParam('location'),
        ));

        // retrieve created job
        $job = Helper::retrieveJob($insertId);
        $job = Helper::format($job, true);

        return new Success($job);
    }
}