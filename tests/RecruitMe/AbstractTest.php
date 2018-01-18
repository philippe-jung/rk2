<?php

namespace Rk\Test\RecruitMe;

use Rk\Config;
use Rk\Test\AbstractApiTest;
use GuzzleHttp\Client;

abstract class AbstractTest extends AbstractApiTest
{
    /**
     * @return Client
     */
    protected function getClient(): Client
    {
        return new Client(array(
            'base_uri' => getenv('BASE_URL') . '/recruitMe/'
        ));
    }

    /**
     * Check that the body has given success content
     *
     * @param array $body
     * @param $expected
     */
    protected function checkSuccessContent(array $body, $expected)
    {
        $this->assertNotEmpty($body);
        $this->assertEquals($body, $expected);
    }
}