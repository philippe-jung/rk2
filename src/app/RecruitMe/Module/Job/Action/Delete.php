<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Application\RecruitMe\Module\Job\Helper;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Response\Success;

class Delete extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_DELETE;

    protected $requiredParams = array(
        'id' => self::FORMAT_INT,
    );

    public function execute(): Response
    {
        // retrieve the job to check it is active
        $job = Helper::retrieveJob($this->getValidatedParam('id'));
        Helper::checkJobIsActive($job);

        // prepare the query
        $binds = array(
            'id' => $this->getValidatedParam('id')
        );
        $query = '
            UPDATE job 
            SET status = "deleted"
            WHERE id = :id';

        // update the job
        DB::getInstance()->update($query, $binds);

        return new Success('Record successfully deleted');
    }
}