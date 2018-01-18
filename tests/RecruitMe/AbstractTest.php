<?php

namespace Rk\Test\RecruitMe;

use Rk\Config;
use Rk\Test\AbstractApiTest;
use GuzzleHttp\Client;

abstract class AbstractTest extends AbstractApiTest
{
    /**
     * @return Client
     * @throws \Rk\Exception\ConfigNotFound
     * @throws \Rk\Exception\Exception
     */
    protected function getClient(): Client
    {
        return new Client(array(
            'base_uri' => Config::getConfigParam('baseUrl') . '/recruitMe/'
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