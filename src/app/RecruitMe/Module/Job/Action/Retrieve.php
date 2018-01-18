<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Application\RecruitMe\Module\Job\Helper;
use Rk\Request;
use Rk\Service\Response\Success;

class Retrieve extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_GET;

    protected $requiredParams = array(
        'id' => self::FORMAT_INT,
    );

    /**
     * @return Response
     * @throws \Rk\DB\Exception
     * @throws \Rk\Exception\Exception
     * @throws \Rk\Service\Exception\Exception
     */
    public function execute(): Response
    {
        // retrieve given job
        $job = Helper::retrieveJob($this->getValidatedParam('id'));

        // make sure it is still active
        Helper::checkJobIsActive($job);

        // format it
        $formattedJob = Helper::format($job);
        return new Success($formattedJob);
    }
}