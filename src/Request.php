<?php
/**
 * PMVC
 *
 * PHP version 5
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  GIT: <git_id>
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC Request
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class Request extends HashMap
{
    /**
     * Http method
     */
    protected $method;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $method = $this->getMethod();
        if ('GET'===$method) {
            $inputs =& $_GET;
        } else {
            $isJsonInput = ('application/json'===getenv('CONTENT_TYPE'));
            if ($isJsonInput || 'PUT'===$method) {
                $input = file_get_contents("php://input");
                if ($isJsonInput) {
                    $inputs = (array)fromJson($input);
                } else {
                    parse_str($input, $inputs);
                }
            } else {
                $inputs =& $_POST;
            }
        }
        parent::__construct($inputs);
    }

    /**
     * Set Method
     *
     * @param string $method method
     *
     * @return string
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        if (empty($this->method)) {
            $method = getenv('REQUEST_METHOD');
            $cros_method = getenv('HTTP_ACCESS_CONTROL_REQUEST_METHOD');
            if ($method === 'OPTIONS' && $cros_method) {
                $method = $cros_method;
            }
            $this->setMethod($method);
        }
        return $this->method;
    }
}
