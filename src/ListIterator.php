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
class ListIterator extends BaseObject implements IteratorAggregate, Countable
{
    /**
     * Walk flag.
     *
     * @var bool 
     */
    protected $walk;

    /**
     * Construct.
     *
     * @param array $state state
     * @param bool  $walk  change sub array to same type.
     */
    public function __construct($state = null, $walk = null)
    {
        $this->state = $this->getInitialState();
        if (!empty($state)) {
            set($this->state, $state);
        }
        if ($walk) {
            $this->walk = $walk;
            $my = get_class($this);
            foreach ($this->state as $k=>$v) {
                if (is_array($v)) {
                    $this->state[$k] = new $my($v, true);
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
    public function getIterator()
    {
        return new ArrayIterator($this->state);
    }

    /**
     * Count.
     *
     * @return int
     */
    public function count()
    {
        return count($this->state);
    }
}
