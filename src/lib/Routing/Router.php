<?php

namespace Rk\Routing;

use Rk\Exception;
use Rk\Config;
use Rk\Action\AbstractAction;
use Rk\Request;

class Router
{
    const ROUTE_DEFAULT  = 'default';
    const ROUTE_NOTFOUND = '404';

    /**
     * Get the action matching the requested URI
     *
     * @param string $applicationName   Name of the application to use when building routes
     * @return AbstractAction
     * @throws Exception\RouteNotFound
     * @throws \Exception
     */
    public static function getActionFromRequest(string $applicationName): AbstractAction
    {
        $uri = self::getUri();

        try {
            // try to get the action from the request URI
            $action = self::getActionFromUri($applicationName, $uri);
        } catch (Exception\RouteNotFound $e) {
            // fallback to the 404 action if any
            $routeInfo = Config::getRoutingParam($applicationName . '.' . self::ROUTE_NOTFOUND);
            if (!empty($routeInfo)) {
                // we only build the 404 action if we found such a route
                // this allows not to go into the full process of finding the good route if it does not exist
                $action = self::getActionFromUri($applicationName, self::ROUTE_NOTFOUND);
            } else {
                throw $e;
            }
        }

        return $action;
    }

    /**
     * @param $applicationName
     * @param $uri
     * @return AbstractAction
     * @throws Exception\RouteNotFound
     * @throws \Exception
     */
    protected static function getActionFromUri($applicationName, $uri): AbstractAction
    {
        $route = self::getRoute($applicationName, $uri);
        $action = self::getActionFromRoute($route);

        return $action;
    }

    /**
     * Get a route object from given application and URI
     *
     * @param string $applicationName
     * @param string $uri
     * @return Route
     * @throws Exception\RouteNotFound
     * @throws \Exception
     */
    protected static function getRoute(string $applicationName, string $uri): Route
    {
        // try to find a route directly matching the URI
        $routeInfo = Config::getRoutingParam($applicationName . '.' . $uri);

        // try to find a dynamic route
        if (empty($routeInfo)) {
            $routeInfo = self::getDynamicRoute($applicationName, $uri);
        }

        // try to use the URI in a "module/action" format
        if (empty($routeInfo)) {
            $parts = explode('/', $uri);
            if (count($parts) >= 2) {
                $routeInfo = array(
                    ucfirst($parts[0]),
                    ucfirst($parts[1]),
                );
            }
        }

        // check if the routeInfo holds specific HTTP methods
        if (!empty($routeInfo)) {
            $routeInfo = self::checkMethodForRoute($routeInfo);
        }

        // no route info found, nothing we can do
        if (empty($routeInfo)) {
            throw new Exception\RouteNotFound('Could not build a route');
        }

        // make sure we have at least an empty array for the route parameters
        if (empty($routeInfo[2])) {
            $routeInfo[2] = array();
        }

        // check that the route info as the desired format
        if (
            empty($routeInfo[0]) || !is_string($routeInfo[0]) ||
            empty($routeInfo[1]) || !is_string($routeInfo[1]) ||
            !is_array($routeInfo[2])
        ) {
            throw new Exception\Exception('Invalid format for routeInfo');
        }

        // build a route object from the retrieved config
        $route = new Route($applicationName, $routeInfo[0], $routeInfo[1], $routeInfo[2]);

        return $route;
    }

    /**
     * @param $applicationName
     * @param $uri
     * @return bool|array
     * @throws \Exception
     *
     * @note some kind of cache would be nice as there will be performance issue with large set of route definitions
     */
    protected static function getDynamicRoute($applicationName, $uri)
    {
        // split $uri by slash so we can check if each of its part match with a dynamic route
        $uriParts = explode('/', $uri);

        // get all routes for the application
        $routes = Config::getRoutingParam($applicationName);

        if (!empty($routes)) {
            foreach ($routes as $routeDefinition => $oneRoute) {
                $routeParts = explode('/', $routeDefinition);   // split the route definition by slash to compare it with the uri
                $parsedParams = array();    // used to save the params parsed from the uri

                if (count($routeParts) != count($uriParts)) {
                    // not even worth checking anything if we do not have the same number of slashes
                    continue;
                }

                foreach ($routeParts as $partIndex => $partValue) {
                    if (0 === strpos($partValue, ':')) {
                        // save the value of the param
                        $parsedParams[$partValue] = $uriParts[$partIndex];
                    } elseif ($partValue != $uriParts[$partIndex]) {
                        // if any none dynamic part of the route does not match the input one, we can try with the next one
                        continue 2;
                    }
                }

                // ensure the found route accepts the request's method
                $oneRoute = self::checkMethodForRoute($oneRoute);
                if (empty($oneRoute)) {
                    continue;
                }

                // we will only reach that point if we found a matching root (thanks to the 2 continue)
                // we have to rename the parameters and remove the starting ":" from each of them
                $paramsForRoute = array();
                foreach ($parsedParams as $name => $value) {
                    $paramsForRoute[substr($name, 1)] = $value;
                }

                // we can build a Route object
                return array($oneRoute[0], $oneRoute[1], $paramsForRoute);
            }
        }

        return false;
    }

    /**
     * Routes can be formatted in 2 different ways:
     * - allows all HTTP methods (no method specified)
     *      'distance' => ['Distance', 'Get'],
     * - expect certain specific HTTP methods (expects either GET or POST)
     *      'job'     => array(
     *          Request::METHOD_GET  => ['Job', 'Collection'],
     *          Request::METHOD_POST => ['Job', 'Create'],
     *      ),
     *
     * @param array $routeInfo
     * @return bool|array
     */
    protected static function checkMethodForRoute(array $routeInfo)
    {
        // the first key of the routeInfo is a string: it means we expect a specific HTTP method
        if (is_string(key($routeInfo))) {
            if (!array_key_exists(Request::getMethod(), $routeInfo)) {
                return false;
            } else {
                $routeInfo = $routeInfo[Request::getMethod()];
            }
        }

        return $routeInfo;
    }

    /**
     * @param Route $route
     * @return mixed
     * @throws Exception\RouteNotFound
     */
    protected static function getActionFromRoute(Route $route): AbstractAction
    {
        $className = 'Rk\Application\\' . $route->getApplication() .
            '\Module\\' . $route->getModule() .
            '\Action\\' . $route->getAction();

        if (!class_exists($className)) {
            throw new Exception\RouteNotFound('Could not find the class for given route');
        }

        return new $className($route->getParams());
    }

    /**
     * Get the requested URI. Defaults to self::DEFAULT_ROUTE
     *
     * @return string
     */
    protected static function getUri(): string
    {
        if (!empty($_SERVER['REDIRECT_URL'])) {
            // use the redirected path if any (for Apache RewriteRules)
            $uri = $_SERVER['REDIRECT_URL'];
        } else {
            // otherwise use PHP_SELF, from which we remove the entry script as we do not want to know the entry file for the routing process
            $uri = $_SERVER['PHP_SELF'];
            if (0 === strpos($uri, $_SERVER['SCRIPT_NAME'])) {
                $uri = str_replace($_SERVER['SCRIPT_NAME'], '', $uri);
            }
        }

        // remove starting and ending /
        $uri = trim($uri, '/');

        // if empty, return the default route
        if ('' === $uri) {
            $uri = self::ROUTE_DEFAULT;
        }
        return $uri;
    }
}