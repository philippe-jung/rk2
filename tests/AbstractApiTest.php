<?php

namespace Rk\Test;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractApiTest extends TestCase
{
    /**
     * @return Client
     */
    abstract protected function getClient(): Client;

    /**
     * Short cut to send requests using Guzzle
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return ResponseInterface
     */
    public function sendRequest(string $method, string $uri, array $params = array())
    {
        $client = $this->getClient();

        // we want to deal with 500 errors manually
        $requestParams = array(
            'http_errors' => false
        );
        if (!empty($params)) {
            $requestParams['query'] = $params;
        }
        $response = $client->request($method, $uri, $requestParams);

        return $response;
    }

    /**
     * Assert that given Guzzle call failed with $message and $code
     *
     * @param ResponseInterface $response
     * @param string $message
     * @param int $code
     */
    public function assertError(ResponseInterface $response, string $message = null, int $code = 500)
    {
        $this->assertEquals($code, $response->getStatusCode());
        if (!empty($message)) {
            $body = json_decode($response->getBody()->getContents(), true);
            $this->assertNotEmpty($body);
            $this->assertArrayHasKey('error', $body);
            $this->assertEquals($message, $body['error']);
        }
    }

    /**
     * Assert that given Guzzle call succeeded and that the returned content has given $expectedKeys
     *
     * @param ResponseInterface $response
     * @param array $expectedKeys
     * @return mixed
     */
    public function assertSuccess(ResponseInterface $response, $expectedKeys = array())
    {
        if (!is_array($expectedKeys)) {
            $expectedKeys = array($expectedKeys);
        }

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody()->getContents(), true);

        if (!empty($expectedKeys)) {
            $this->assertNotEmpty($body);

            // check that we have all expected keys in the returned content
            $checkedBody = $body;
            foreach ($expectedKeys as $oneKey) {
                $this->assertArrayHasKey($oneKey, $checkedBody);
                unset($checkedBody[$oneKey]);
            }

            // check no extra key were returned in the body
            $this->assertArrayEmpty($checkedBody);
        }

        return $body;
    }

    /**
     * Check if an array is empty, and displays its keys if it is not
     *
     * @param array $array
     */
    public function assertArrayEmpty(array $array)
    {
        if (!empty($array)) {
            $this->fail(
                'Failed asserting that an array is empty' . PHP_EOL .
                'Existing keys: ' . implode(', ', array_keys($array))
            );
        }
    }
}