<?php

namespace Rk\Test\ExampleApi;

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
            'base_uri' => Config::getConfigParam('github.root')
        ));
    }
}