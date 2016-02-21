<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @version GIT: <git_id>
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC Request.
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class Request extends HashMap
{
    /**
     * Request method.
     */
    protected $method;

    /**
     * Set Method.
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
     * Get Method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
}
