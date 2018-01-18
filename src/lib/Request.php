<?php

namespace Rk;


class Request
{
    const METHOD_POST   = 'POST';
    const METHOD_GET    = 'GET';
    const METHOD_PUT    = 'PUT';
    const METHOD_PATCH  = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var array
     */
    private static $params;

    /**
     * Get all the params of the request
     *
     * @return array
     */
    public static function getParams(): array
    {
        if (empty(self::$params)) {
            // retrieve standard GET/POST params
            $params = $_REQUEST;

            $vars = array();
            // add other types of params
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                if (!empty($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $vars = json_decode($input, true);
                } else {
                    parse_str($input, $vars);
                }
            }

            // override GET/POST params with the input ones
            foreach ($vars as $name => $value) {
                $params[$name] = $value;
            }

            self::$params = $params;
        }

        return self::$params;
    }

    /**
     * Get the value of param $name or fallback to $default
     *
     * @param $name
     * @param null $default
     * @return mixed
     */
    public static function getParam($name, $default = null)
    {
        if (array_key_exists($name, $_REQUEST)) {
            return $_REQUEST[$name];
        }

        return $default;
    }

    /**
     * Does param $name exist in the request?
     *
     * @param $name
     * @return bool
     */
    public static function hasParam($name): bool
    {
        if (array_key_exists($name, $_REQUEST)) {
            return true;
        }

        return false;
    }

    /**
     * Get the HTTP request method
     *
     * @return mixed
     */
    public static function getMethod(): string
    {
        if (Config::isDebug()) {
            // this allows to specify a method even if using only GET queries
            $method = self::getParam('method');
        }
        if (empty($method)) {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        return $method;
    }
}