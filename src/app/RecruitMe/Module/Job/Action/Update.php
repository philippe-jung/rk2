<?php

namespace Rk\Application\RecruitMe\Module\Job\Action;


use Rk\Action\AbstractAction;
use Rk\Action\Response;
use Rk\Application\RecruitMe\Module\Job\Helper;
use Rk\DB\DB;
use Rk\Request;
use Rk\Service\Exception\Exception;
use Rk\Service\Response\Error;
use Rk\Service\Response\Success;

class Update extends AbstractAction
{
    protected $requiredMethod = Request::METHOD_PUT;

    protected $requiredParams = array(
        'id' => self::FORMAT_INT,
    );

    protected $optionalParams = array(
        'title'       => self::FORMAT_STRING,
        'category'    => self::FORMAT_STRING,
        'description' => self::FORMAT_STRING,
        'location'    => self::FORMAT_STRING,
    );

    /**
     * @return Response
     * @throws \Rk\DB\Exception
     * @throws \Rk\Service\Exception\Exception
     * @throws \Rk\Exception\Exception
     */
    public function execute(): Response
    {
        // check which attribute we must update
        $toUpdate = array();
        foreach ($this->optionalParams as $name => $format) {
            $value = $this->getValidatedParam($name);
            if (!is_null($value)) {
                $toUpdate[$name] = $value;
            }
        }

        // check that at least one attribute needs to be updated
        if (empty($toUpdate)) {
            return new Error('No new value was specified');
        }

        // retrieve the job to ensure it is active
        $job = Helper::retrieveJob($this->getValidatedParam('id'));
        Helper::checkJobIsActive($job);

        // prepare the query
        $binds = array(
            'id' => $this->getValidatedParam('id')
        );
        $query = '
            UPDATE job 
            SET ';
        foreach ($toUpdate as $field => $value) {
            $binds[$field] = $value;
            $query .= $field . ' = :' . $field;
        }
        $query .= '
            WHERE id = :id';

        // update the job
        DB::getInstance()->update($query, $binds);

        // then retrieve it again with updated values
        $job = Helper::retrieveJob($this->getValidatedParam('id'));
        $job = Helper::format($job);

        return new Success($job);
    }
}