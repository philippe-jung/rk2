<?php

namespace Rk;


use Rk\Exception\ConfigNotFound;
use Rk\Exception\Exception;

class Config
{
    /**
     * Get a param from the config
     *
     * @param string $paramName Name of the param to retrieve
     * @param string $default   Front value if param is not set in the config
     * @return mixed
     * @throws ConfigNotFound
     * @throws Exception
     */
    public static function getConfigParam(string $paramName, $default = null)
    {
        try {
            $value = self::getParam($paramName, 'config');
        } catch (ConfigNotFound $e) {
            if (is_null($default)) {
                // if no default value was given, throw an exception
                throw $e;
            } else {
                // if a default value was given, use it if the config is not found
                $value = $default;
            }
        }

        return $value;
    }

    /**
     * Get the config for given route
     * It should be an array (string <module>, string <action>, [optional associative array <params>])
     *
     * @param string $routeName
     * @return mixed
     * @throws \Exception
     */
    public static function getRoutingParam(string $routeName)
    {
        try {
            $value = self::getParam($routeName, 'routing');
        } catch (ConfigNotFound $e) {
            $value = false;
        }

        return $value;
    }

    /**
     * @param string $paramName Name of the parameter to look for
     * @param string $section   Index of $GLOBALS in which to search for the parameter
     * @return mixed
     * @throws ConfigNotFound
     * @throws Exception
     */
    protected static function getParam(string $paramName, string $section)
    {
        if (!array_key_exists($section, $GLOBALS)) {
            throw new Exception('Config section not found: ' . $section);
        }

        // split the param name by . to allow to retrieve specific parts of an array
        $parts = explode('.', $paramName);

        // loop on each part
        $searchIn = $GLOBALS[$section];
        foreach ($parts as $onePart) {
            if (!array_key_exists($onePart, $searchIn)) {
                // if any part is missing, throw an exception
                throw new ConfigNotFound($paramName);
            }
            $searchIn = $searchIn[$onePart];
        }

        return $searchIn;
    }

    /**
     * Are we in a debug environment?
     *
     * @return bool
     */
    public static function isDebug(): bool
    {
        return !empty($GLOBALS['debug']);
    }
}