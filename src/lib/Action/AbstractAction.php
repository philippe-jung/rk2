<?php

namespace Rk\Action;


use Rk\Service\Exception;
use Rk\Request;

abstract class AbstractAction
{
    abstract public function execute(): Response;

    const FORMAT_STRING     = 'string';
    const FORMAT_INT        = 'integer';
    const FORMAT_FLOAT      = 'float';
    const FORMAT_EMAIL      = 'email';
    const FORMAT_PHONE      = 'phone number';
    const FORMAT_COORDINATE = 'coordinate';

    /**
     * "Name => Value" array of validated parameters that were submitted
     *
     * @var array
     */
    protected $params;

    /**
     * Description of the required parameters and their type
     * Format: array(
     *      'paramName' => format (one of the FORMAT_* constant)
     *
     * @var array
     */
    protected $requiredParams = array();

    /**
     * Description of the optional parameters and their type
     * Format: array(
     *      'paramName' => format (one of the FORMAT_* constant)
     *
     * @var array
     */
    protected $optionalParams = array();

    /**
     * Required HTTP request method for the action
     * Must be a \Rk\Request::METHOD_* constant, or an array of such constants if the service accepts several methods
     * Defaults to null, which means all methods are accepted
     *
     * @var string|null
     */
    protected $requiredMethod;

    /**
     * AbstractServiceAction constructor.
     *
     * @param array $paramsFromRoute    params retrieved from the uri at the router level
     * @throws Exception\InvalidType
     * @throws Exception\MissingParam
     * @throws \Exception
     */
    public function __construct(array $paramsFromRoute = array())
    {
        $this->validateMethod();
        $this->validateParams($paramsFromRoute);
    }

    /**
     * Check the HTTP method is valid
     *
     * @throws Exception\Exception
     */
    protected function validateMethod()
    {
        if (!empty($this->requiredMethod)) {
            $requestedMethod = Request::getMethod();

            if (!is_array($this->requiredMethod)) {
                $this->requiredMethod = array($this->requiredMethod);
            }

            if (!in_array(strtoupper($requestedMethod), $this->requiredMethod)) {
                throw new Exception\Exception('This service does not accept the ' . $requestedMethod . ' method');
            }
        }
    }

    /**
     * Validate the parameters submitted
     *
     * @param array $paramsFromRoute    params retrieved from the uri at the router level
     * @throws Exception\InvalidType
     * @throws Exception\MissingParam
     * @throws \Exception
     */
    protected function validateParams(array $paramsFromRoute = array())
    {
        $params = Request::getParams();
        $params = array_merge($params, $paramsFromRoute);

        foreach ($this->requiredParams as $paramName => $paramType) {
            // check that the param has been submitted
            if (!array_key_exists($paramName, $params) || "" === $params[$paramName]) {
                throw new Exception\MissingParam($paramName);
            }

            $this->validateOneParam($params, $paramName, $paramType);
        }

        foreach ($this->optionalParams as $paramName => $paramType) {
            // check that the param has been submitted
            if (array_key_exists($paramName, $params)) {
                $this->validateOneParam($params, $paramName, $paramType);
            }
        }
    }

    /**
     * @param array $params     All params from request
     * @param string $paramName
     * @param string $paramType
     * @throws Exception\InvalidType
     * @throws \Exception
     */
    protected function validateOneParam(array $params, string $paramName, string $paramType)
    {
        $submittedParam = $params[$paramName];

        // check its type is valid
        $valid = $this->isValidParam($paramType, $submittedParam);
        if (!$valid) {
            throw new Exception\InvalidType($paramName . ' should be a valid ' . $paramType);
        }

        // save the param
        $this->params[$paramName] = $submittedParam;
    }

    /**
     * @param $paramType
     * @param $submittedParam
     * @return bool
     * @throws \Exception
     */
    protected function isValidParam($paramType, $submittedParam)
    {
        $goodType = true;
        // check that the param type is valid
        switch ($paramType) {
            case self::FORMAT_STRING:
                if (!is_string($submittedParam)) {
                    $goodType = false;
                }
                break;

            case self::FORMAT_PHONE:
                if (!preg_match('/^\+[0-9]*$/i', $submittedParam)) {
                    $goodType = false;
                }
                break;

            case self::FORMAT_EMAIL:
                if (!filter_var($submittedParam, FILTER_VALIDATE_EMAIL)) {
                    $goodType = false;
                }
                break;

            case self::FORMAT_INT:
                if (!filter_var($submittedParam, FILTER_VALIDATE_INT)) {
                    $goodType = false;
                }
                break;

            case self::FORMAT_COORDINATE:
            case self::FORMAT_FLOAT:
                if (!filter_var($submittedParam, FILTER_VALIDATE_FLOAT)) {
                    $goodType = false;
                }
                break;

            default:
                throw new \Exception('Unknown type ' . $paramType);
        }

        return $goodType;
    }

    /**
     * Get the value of given validated param
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function getValidatedParam(string $name, $default = null)
    {
        if (!array_key_exists($name, $this->params)) {
            return $default;
        }
        return $this->params[$name];
    }
}