<?php

namespace Rk\Test\Framework;

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
            'base_uri' => getenv('BASE_URL')
        ));
    }
}