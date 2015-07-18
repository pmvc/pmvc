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
 * @link     http://pear.php.net/package/PackageName
 */
namespace PMVC;

/**
 * PMVC Request
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
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
        if ('GET'===$this->getMethod()) {
            set($this, $_GET);
        } else {
            set($this, $_POST);
        }
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
            $this->setMethod(getenv('REQUEST_METHOD'));
        }
        return $this->method;
    }
}
