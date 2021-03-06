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
        'id' => self::FORMAT_STRING,
    );

    /**
     * @return Response
     * @throws \Rk\DB\Exception\Exception
     * @throws \Rk\Exception\Exception
     * @throws \Rk\Service\Exception\Exception
     */
    public function execute(): Response
    {
        // retrieve the job to check it is active
        $job = Helper::retrieveJob($this->getValidatedParam('id'));
        Helper::checkJobIsActive($job);

        // prepare the query
        $binds = array(
            'public_id' => $this->getValidatedParam('id'),
            'status'    => Helper::STATUS_DELETED,
        );
        $query = '
            UPDATE job 
            SET status = :status
            WHERE public_id = :public_id';

        // mark the job as deleted
        DB::getInstance()->update($query, $binds);

        return new Success('Record successfully deleted');
    }
}