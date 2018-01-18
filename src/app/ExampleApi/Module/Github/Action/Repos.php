<?php

namespace Rk\Application\ExampleApi\Module\Github\Action;

use Rk\Config;
use Rk\Service\Response\Error;
use Rk\Service\Response\Response;
use Rk\Service\Response\Success;
use Rk\Service\AbstractServiceAction;
use Rk\Service\Exception\Exception;

class Repos extends AbstractServiceAction
{
    protected $requiredParams = array(
        'id' => self::FORMAT_INT,
    );

    /**
     * @return Response
     * @throws Exception
     * @throws \Rk\Exception\Exception
     * @throws \Rk\Exception\ConfigNotFound
     */
    public function execute(): \Rk\Action\Response
    {
        $contribs = Config::getConfigParam('mockup.contribs');
        $repos = Config::getConfigParam('mockup.repos');
        $repoId = $this->getValidatedParam('id');

        // check errors
        if (!array_key_exists($repoId, $repos)) {
            return new Error('No such repo');
        }
        if (empty($repos[$repoId])) {
            return new Error('Noone has contributed to this repo');
        }

        // build the return
        $return = array();
        foreach ($contribs as $userName => $reposForUser) {
            if (in_array($repoId, $reposForUser)) {
                // if we found our repo in the list for the user
                $return[] = $userName;
            }
        }

        return new Success($return);
    }
}