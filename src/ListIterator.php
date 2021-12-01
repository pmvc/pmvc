<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Data
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

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Serializable;

/**
 * ListIterator.
 *
 * @category Data
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class ListIterator extends BaseObject implements
    Serializable,
    Countable,
    IteratorAggregate
{
    /**
     * Construct.
     *
     * @param array $state state
     * @param bool  $walk  change sub array to same type.
     */
    public function __construct($state = null, $walk = null)
    {
        $this->state = $this->getInitialState();
        if (is_string($state)) {
            $state = unserialize($state);
        }
        if (!empty($state)) {
            set($this->state, $state);
        }
        $hashMapAllClass = 'PMVC\HashMapAll';
        if ($walk || $this instanceof $hashMapAllClass) {
            foreach ($this->state as $k=>$v) {
                if (is_array($v)) {
                    $this->state[$k] = new static($v, true);
                }
            }
        }
    }

    /**
     * Get Initial State.
     *
     * @return array
     */
    protected function getInitialState()
    {
        return [];
    }

    /**
     * GetIterator.
     *
     * @return ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->state);
    }

    /**
     * Count.
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->state);
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString()
    {
        return serialize($this);
    }

    /**
     * Magic Serializable.
     *
     * @return array
     */
    public function __serialize()
    {
        return $this->state;
    }

    /**
     * Serializable.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->state);
    }

    /**
     * Magic UnSerialize.
     *
     * @param array $state state
     *
     * @return void
     */
    public function __unserialize($state)
    {
        $this->state = $state;
    }

    /**
     * UnSerialize.
     *
     * @param array $state state
     *
     * @return void
     */
    public function unserialize($state)
    {
        $this->state = unserialize($state);
    }
}
