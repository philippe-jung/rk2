<?php

namespace Rk\Application\RecruitMe\Module\Job;


use Rk\DB\DB;
use Rk\DB\Exception as DBException;
use Rk\Exception\Exception;
use Rk\Service\Exception\Exception as ServiceException;

class Helper
{
    // constant for the possible ENUM values of the status job.status field
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';

    /**
     * Get the first job matching the given parameter
     *
     * @param string $value
     * @param string $fieldName
     * @return mixed
     * @throws DBException
     * @throws ServiceException
     */
    public static function retrieveJob(string $value, string $fieldName = 'id')
    {
        $query = '
            SELECT id, title, category, description, location, created_at, status
            FROM job 
            WHERE ' . $fieldName . ' = ?
            LIMIT 1';

        $jobs = DB::getInstance()->select($query, $value);

        if (!empty($jobs[0])) {
            return $jobs[0];
        }

        throw new ServiceException('No such job');
    }

    /**
     * Format the job fields
     *
     * @param array $values
     * @param bool $partialFields if true, it means we only want some partial selection of fields
     * @return array
     */
    public static function format(array $values, bool $partialFields = false): array
    {
        if (!$partialFields) {
            $date = new \DateTime($values['created_at']);
            $return = array(
                '_id'         => $values['id'],
                'title'       => $values['title'],
                'category'    => $values['category'],
                'description' => $values['description'],
                'location'    => $values['location'],
                'date'        => $date->format('Y-m-d\TH:i:s.v\Z'),
            );
        } else {
            $return = array(
                'title'       => $values['title'],
                'category'    => $values['category'],
                'description' => $values['description'],
                'location'    => $values['location'],
            );
        }

        return $return;
    }

    /**
     * @param array $job
     * @throws Exception
     * @throws ServiceException
     */
    public static function checkJobIsActive(array $job)
    {
        if (empty($job['status'])) {
            throw new Exception('No status defined on the job');
        }

        if ($job['status'] != Helper::STATUS_ACTIVE) {
            throw new ServiceException('The requested job is no longer active');
        }
    }
}