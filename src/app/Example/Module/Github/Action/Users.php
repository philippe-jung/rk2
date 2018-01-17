<?php

namespace Rk\Application\Example\Module\Github\Action;

use Rk\Config;
use Rk\Service\Response\Error;
use Rk\Service\Response\Response;
use Rk\Service\Response\Success;
use Rk\Service\AbstractServiceAction;
use Rk\Service\Exception\Exception;

class Users extends AbstractServiceAction
{
    protected $requiredParams = array(
        'username' => self::FORMAT_STRING,
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
        $userName = $this->getValidatedParam('username');

        // check errors
        if (!array_key_exists($userName, $contribs)) {
            return new Error('No such user');
        }
        if (empty($contribs[$userName])) {
            return new Error('User has no contribution');
        }

        // build the return
        $repos = Config::getConfigParam('mockup.repos');
        $return = array();
        foreach ($contribs[$userName] as $repoId) {
            if (empty($repos[$repoId])) {
                throw new Exception('Unknown repo ' . $repoId);
            }
            $return[] = array(
                'id'   => $repoId,
                'name' => $repos[$repoId],
            );
        }
        return new Success($return);
    }
}