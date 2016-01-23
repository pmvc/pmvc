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
use SplFixedArray;

/**
 * PMVC ImmutableArray
 * What is overloading?
 * http://php.net/manual/en/language.oop5.overloading.php
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class ImmutableArray extends HashMap
{
    protected $max=0;
    protected $size=0;

    /**
     * Construct
     *
     * @param array $state state 
     *
     * @return ArrayIterator
     */
    public function __construct($state=null)
    {
        if (!empty($state) && is_array($state)) {
            $this->max = count($state);
            $this->state = $this->getInitialState();
            set($this, $state);
        } else {
            $this->state = $this->getInitialState();
        }
    }

    /**
     * Get Initial State 
     *
     * @return array 
     */
    protected function getInitialState()
    {
        return new SplFixedArray($this->max);
    }

    /**
     * Set
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return boolean
     */
    public function offsetSet($k, $v)
    {
        if (!isset($this->keys[$k])) {
            $this->keys[$k] = $this->size;
            $this->size++;
        }
        return $this->state[$this->keys[$k]]
            = new Object($v);
    }
}
