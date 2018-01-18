<?php

namespace Rk\Application\ExampleApi\Module\Distance\Action;

use Rk\Application\ExampleApi\Module\Distance\Tree\Element;
use Rk\Application\ExampleApi\Module\Distance\Tree\GetHelper;
use Rk\Request;
use Rk\Service\AbstractServiceAction;
use Rk\Service\Response\Response;
use Rk\Service\Response\Success;
use Rk\Service\Exception\Exception;

class Get extends AbstractServiceAction
{
    protected $requiredParams = array(
        'user1' => self::FORMAT_STRING,
        'user2' => self::FORMAT_STRING,
    );

    protected $optionalParams = array(
        'user3' => self::FORMAT_STRING,
        'user4' => self::FORMAT_STRING,
    );

    protected $requiredMethod = array(
        Request::METHOD_GET,
    );

    /**
     * @return Response
     * @throws Exception
     * @throws \Rk\Exception\ConfigNotFound
     */
    public function execute(): \Rk\Action\Response
    {
        $user1 = $this->getValidatedParam('user1');
        $user2 = $this->getValidatedParam('user2');

        $treeHelper = new GetHelper($user1, $user2);
        $element = $treeHelper->searchConnection();

        return $this->getSuccessResponse($user1, $element);
    }

    /**
     * Get the details of connection for given Element
     * Return all users (with the connecting repo) from the lowest level to the highest one
     *
     * @param Element $element
     * @return array
     */
    protected function getPathDetails(Element $element)
    {
        $return = array();

        do {
            $return[] = $element->getUserName() . ' (via ' . $element->getRepoName() . ')';
        } while (!empty(($element = $element->getParent())));

        return array_reverse($return);
    }

    /**
     * Create a Success Response, with distance and path details
     *
     * @param Element $element
     * @return Success
     */
    protected function getSuccessResponse($user1, Element $element)
    {
        $details = $this->getPathDetails($element);
        array_shift($details);  // we remove the level 0, as we include it manually without the repo name
        $path = $user1 . ' -> ' . implode(' -> ', $details);

        return new Success(array(
            'distance' => count($details),
            'path'     => $path
        ));
    }
}