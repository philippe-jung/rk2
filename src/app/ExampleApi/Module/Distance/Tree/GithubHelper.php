<?php

namespace Rk\Application\ExampleApi\Module\Distance\Tree;

use Rk\Config;
use Rk\Service\Exception\Exception;
use GuzzleHttp\Client;

/**
 * Simple connector to the GitHub services
 * @todo Use it via dependency injection
 */
class GithubHelper
{
    /**
     * Used to cache the data retrieved via the services
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Get an array of repos the user has contributed to ("id => name" format)
     *
     * @param string $userName
     * @return array
     * @throws Exception
     * @throws \Rk\Exception\ConfigNotFound
     * @throws \Rk\Exception\Exception
     */
    public function getReposForUser(string $userName): array
    {
        if (empty($this->cache['getReposForUser'])) {
            $this->cache['getReposForUser'] = array();
        }

        // create cache for service and userName if needed
        if (empty($this->cache['getReposForUser'][$userName])) {
            $uri = 'github/users?username=' . rawurlencode($userName);
            $results = $this->queryGithub($uri);

            // build the list of repos user has contributed to
            $contributedRepos = array();
            foreach ($results as $oneResult) {
                $contributedRepos[$oneResult['id']] = $oneResult['name'];
            }

            $this->cache['getReposForUser'][$userName] = $contributedRepos;
        }

        // return cached data
        return $this->cache['getReposForUser'][$userName];
    }

    /**
     * Get the list of users that contributed to given repo
     *
     * @param int $repoId
     * @return array
     * @throws Exception
     * @throws \Rk\Exception\ConfigNotFound
     * @throws \Rk\Exception\Exception
     */
    public function getContributorsForRepo(int $repoId)
    {
        if (empty($this->cache['getContributorsForRepo'])) {
            $this->cache['getContributorsForRepo'] = array();
        }

        // create cache for service and repoId if needed
        if (empty($this->cache['getContributorsForRepo'][$repoId])) {
            $uri = 'github/repos?id=' . rawurlencode($repoId);
            $results = $this->queryGithub($uri);

            // build the list of repos user has contributed to
            $contributors = array();
            foreach ($results as $oneResult) {
                $contributors[] = $oneResult;
            }

            $this->cache['getContributorsForRepo'][$repoId] = $contributors;
        }

        // return cached data
        return $this->cache['getContributorsForRepo'][$repoId];
    }

    /**
     * Query github and return the json decoded data as an array
     *
     * @param string $uri
     * @return array|mixed
     * @throws Exception
     * @throws \Rk\Exception\Exception
     * @throws \Rk\Exception\ConfigNotFound
     */
    protected function queryGithub(string $uri)
    {
        $client = new Client(array(
            'base_uri' => Config::getConfigParam('github.root'),
        ));

        $requestParams = array(
            'http_errors' => false,     // we want to deal with errors manually
        );

        $response = $client->request('GET', $uri, $requestParams);
        $contents = $response->getBody()->getContents();
        if (!empty($contents)) {
            $output = json_decode($contents, true);
            if (!is_array($output)) {
                // we could not decode the JSON
                throw new Exception('An error has occurred when querying github: the response was not proper JSON');
            }
        } else {
            $output = array();
        }

        if ($response->getStatusCode() !== 200) {
            // Display the message if an error occured
            $message = 'An error has occurred when querying github';
            $message .= ': ' . $response->getStatusCode();
            if (!empty($output['message'])) {
                $message .= ' - ' . $output['message'];
            }
            throw new Exception($message);
        }

        return $output;
    }
}