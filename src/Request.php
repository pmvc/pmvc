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
     * Get Method
     *
     * @return string
     */
    public function getMethod()
    {
        return getenv('REQUEST_METHOD');
    }
}
