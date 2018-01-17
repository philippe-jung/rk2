<?php

namespace Rk\Test\Framework;

require_once (__DIR__ . '/AbstractTest.php');

class Routing extends AbstractTest
{
    /**
     * Test invalid use of the service
     */
    public function testCallErrors()
    {
        // 404 on unknown route
        $response = $this->sendRequest('GET', 'notAKnownRoute');
        $this->assertError($response, null, 404);

        // 200 on homepage (when calling the script name directly)
        $response = $this->sendRequest('GET', '/index.php');
        $this->assertSuccess($response);

        // 200 on homepage (when omitting the script name and served via http redirect)
        $response = $this->sendRequest('GET', '/');
        $this->assertSuccess($response);

        // 200 when using a "module/action" format URI for an existing module/action
        $response = $this->sendRequest('GET', '/home/index');
        $this->assertSuccess($response);

        // 404 when using a "module/action" format URI for an not existing module/action
        $response = $this->sendRequest('GET', '/home/indexx');
        $this->assertError($response, null, 404);

        // 200 when using a "module/action" format URI for an existing module/action (with script name)
        $response = $this->sendRequest('GET', '/index.php/home/index');
        $this->assertSuccess($response);

        // 404 when using a "module/action" format URI for an not existing module/action (with script name)
        $response = $this->sendRequest('GET', '/index.php/home/indexx');
        $this->assertError($response, null, 404);
    }

}