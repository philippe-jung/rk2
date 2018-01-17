<?php

namespace Rk\Service;

use Rk\Request;
use Rk\Action\DispatcherInterface;
use Rk\Service\Response\Error;
use Rk\Service\Exception;

class ServiceDispatcher implements DispatcherInterface
{
    /**
     * Execute the service matching the Request Params
     *
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $service = $this->getServiceObject();
            $response = $service->execute();
        } catch (Exception\Exception $e) {
            $response = new Error($e->getMessage());
        }
        $response->send();
    }

    /**
     * Return the service that matches the Request Method
     *
     * @return AbstractServiceAction
     * @throws Exception\UnsupportedMethod
     * @throws \Exception
     */
    protected function getServiceObject(): AbstractServiceAction
    {
        $method = Request::getMethod();
        $baseNamespace = get_class($this);
        $dispatcherClassName = substr(strrchr($baseNamespace, "\\"), 1);

        $serviceClassName = str_replace($dispatcherClassName, ucfirst(strtolower($method)), $baseNamespace);

        // if the class does not exist, that means the type of request is not supported
        if (!class_exists($serviceClassName)) {
            throw new Exception\UnsupportedMethod($method);
        }

        $service = new $serviceClassName();
        // if the class is not an AbstractServiceAction, something is wrong on our side
        if (!$service instanceof AbstractServiceAction) {
            throw new \Exception($serviceClassName . ' is not an AbstractServiceAction');
        }

        return $service;
    }
}