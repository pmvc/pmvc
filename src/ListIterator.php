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
 * ListIterator
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class ListIterator extends Object
    implements \IteratorAggregate, \Countable
{
    protected $values=array();

    /**
     * GetIterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    /**
     * Construct
     *
     * @param array $values values
     *
     * @return ArrayIterator
     */
    public function __construct($values=null)
    {
        if (is_array($values)) {
            $this->offsetUnset($values);
        }
    }

    /**
     * Clean
     *
     * @param mixed $k key
     *
     * @return boolean
     */
    public function offsetUnset($k=null)
    {
        return clean($this->values, $k);
    }

    /**
     * Count
     *
     * @return int
     */
    public function count()
    {
        return count($this->values);
    }
}
