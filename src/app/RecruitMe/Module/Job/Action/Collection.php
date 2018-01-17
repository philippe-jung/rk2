<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Application\RecruitMe\Module\Job\Helper;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Response\Success;

class Collection extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_GET;

    public function execute(): Response
    {
        // retrieve all active jobs
        $query = '
            SELECT title, category, description, location
            FROM job 
            WHERE status = "' . Helper::STATUS_ACTIVE. '"';

        $jobs = DB::getInstance()->select($query);

        $formattedJobs = array();
        foreach ($jobs as $oneJob) {
            $formattedJobs[] = Helper::format($oneJob, true);
        }

        return new Success($formattedJobs);
    }
}