<?php

namespace Rk\Routing;

/**
 * Simple representation of an internal route
 * The application, module and action will match with a class name
 * Example:
 *      application = Front
 *      module = Home
 *      action = Index
 *  => \Rk\Application\Front\Module\Home\Action\Index
 */
class Route
{
    /**
     * @var string
     */
    protected $application;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $params;

    /**
     * Route constructor.
     * @param string $application
     * @param string $module
     * @param string $action
     * @param array $params associative array of parameters retrieved from the route
     */
    public function __construct(string $application, string $module, string $action, array $params = array())
    {
        $this->application = $application;
        $this->module = $module;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getApplication(): string
    {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}