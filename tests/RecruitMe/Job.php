<?php

namespace Rk\Test\RecruitMe;


use Rk\DB\DB;

require_once (__DIR__ . '/AbstractTest.php');

class Job extends AbstractTest
{
    /**
     * @throws \Rk\DB\Exception\Exception
     */
    public static function setUpBeforeClass()
    {
        // clean the DB
        DB::getInstance()->query('DELETE FROM job');

        require_once (__DIR__ . '/jobFixtures.php');

        foreach ($JOBS_DEFINITION as $oneJob) {
            self::$jobs[] = $oneJob;
        }
    }

    /**
     * Data for jobs used
     *
     * @var array
     */
    protected static $jobs = array();

    /**
     * Return given job (load it from the fixtures if it has not been used yet)
     *
     * @param int $jobNumber
     * @return mixed
     */
    public function getJob(int $jobNumber): array
    {
        return self::$jobs[$jobNumber];
    }

    /**
     * Remove the fields not used in "short" format
     *
     * @param $job
     * @return mixed
     */
    public function getShortFormat(array $job): array
    {
        unset($job['_id'], $job['date']);

        return $job;
    }

    public function testCallErrors()
    {
        // Unknown service
        $response = $this->sendRequest('GET', 'notAnEndPoint');
        $this->assertError($response, 'No such endpoint');

        // Incorrect method
        $response = $this->sendRequest('DELETE', 'job');
        $this->assertError($response, 'No such endpoint');
    }

    /**
     * @throws \Rk\DB\Exception\Exception
     * @depends testCallErrors
     */
    public function testCreate()
    {
        // we check that the collection service return an empty list at start
        $response = $this->sendRequest('GET', 'job');
        $result = $this->assertSuccess($response);
        $this->assertEmpty($result);

        // Call the retrieve service without a valid integer
        $response = $this->sendRequest('POST', 'job/id');
        $this->assertError($response, 'No such endpoint');

        // Create without a location
        $job = $this->getJob(1);
        unset($job['location']);
        $response = $this->sendRequest('POST', 'job', $job);
        $this->assertError($response, 'Missing parameter location');

        // Create with all good params
        for ($i = 0; $i < count(self::$jobs); $i++) {
            $job = $this->getJob($i);
            $response = $this->sendRequest('POST', 'job', $job);
            $result = $this->assertSuccess($response, $job);

            // add the values not returned by this service but needed by others
            $res = DB::getInstance()->select('
                SELECT 
                    public_id,
                    created_at 
                FROM job
                ORDER BY id DESC
                LIMIT 1'
            );
            self::$jobs[$i]['_id']  = $res[0]['public_id'];
            $date = new \DateTime($res[0]['created_at']);
            self::$jobs[$i]['date'] = $date->format('Y-m-d\TH:i:s.v\Z');
        }
    }

    /**
     * @depends testCreate
     */
    public function testCollection()
    {
        // we should find all entries of self::$jobs in the collection service
        $response = $this->sendRequest('GET', 'job');
        $result = $this->assertSuccess($response);
        $this->assertEquals(count($result), count(self::$jobs));

        // also check that entries are return in "short" format for the first entry
        $valuesToFind = $this->getShortFormat(self::$jobs[0]);
        $this->assertEquals($result[0], $valuesToFind);
    }

    /**
     * @depends testCollection
     */
    public function testRetrieve()
    {
        // check that we can retrieve all jobs
        for ($i = 0; $i < count(self::$jobs); $i++) {
            $job = $this->getJob($i);
            $response = $this->sendRequest('GET', 'job/' . $job['_id']);
            $result = $this->assertSuccess($response, $job);
        }

        // try to retrieve an unexisting job
        $response = $this->sendRequest('GET', 'job/' . ($job['_id'] + 1));
        $this->assertError($response, 'No such job');
    }

    /**
     * @depends testRetrieve
     */
    public function testDelete()
    {
        // delete the first job
        $response = $this->sendRequest('DELETE', 'job/' . self::$jobs[0]['_id']);
        $result = $this->assertSuccess($response, array('message' => 'Record successfully deleted'));

        // try to retrieve the deleted job
        $response = $this->sendRequest('GET', 'job/' . self::$jobs[0]['_id']);
        $this->assertError($response, 'The requested job is no longer active');

        // try to delete the first job again
        $response = $this->sendRequest('DELETE', 'job/' . self::$jobs[0]['_id']);
        $this->assertError($response, 'The requested job is no longer active');
    }

    /**
     * @depends testDelete
     */
    public function testUpdate()
    {
        // try to update the deleted job
        $response = $this->sendRequest('PUT', 'job/' . self::$jobs[0]['_id'], array(
            'location' => 'Paris, France'
        ));
        $this->assertError($response, 'The requested job is no longer active');

        // update the second job
        $response = $this->sendRequest('PUT', 'job/' . self::$jobs[1]['_id'], array(
            'location' => 'Paris, France'
        ));
        self::$jobs[1]['location'] = 'Paris, France';
        $this->assertSuccess($response, self::$jobs[1]);

        // retrieve the updated job
        $response = $this->sendRequest('GET', 'job/' . self::$jobs[1]['_id']);
        $this->assertSuccess($response, self::$jobs[1]);
    }

    /**
     * @depends testUpdate
     */
    public function testCollectionAgain()
    {
        // we should find all entries of self::$jobs in the collection service, except the deleted one
        $response = $this->sendRequest('GET', 'job');
        $result = $this->assertSuccess($response);
        $toFind = self::$jobs;
        unset($toFind[0]);

        $nbReturned = count($result);
        $this->assertEquals($nbReturned, count($toFind));

        // check that all data are identical
        foreach ($result as $oneResult) {
            $this->assertEquals($oneResult, $this->getShortFormat(current($toFind)));
            next($toFind);
        }
    }

    /**
     * @depends testCollectionAgain
     */
    public function testNaughtyString()
    {
        // check that the Update service deals with a list of dodgy strings
        $source = __DIR__ . '/BigListOfNaughtyStrings.txt';

        $fh = fopen($source, 'r');
        while (($buffer = fgets($fh, 4096)) !== false) {
            if (0 !== strpos($buffer, '#') && $buffer != "\n") {
                $response = $this->sendRequest('PUT', 'job/' . self::$jobs[1]['_id'], array(
                    'description' => $buffer
                ));
                $result = $this->assertSuccess($response);
                $this->assertEquals($result['description'], $buffer);
            }
        }
        fclose($fh);
    }

}
