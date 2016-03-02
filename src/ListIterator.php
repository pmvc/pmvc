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

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * ListIterator.
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
class ListIterator extends Object implements IteratorAggregate, Countable
{
    /**
     * Construct.
     *
     * @param array $state state
     *
     * @return ArrayIterator
     */
    public function __construct($state = null)
    {
        $this->state = $this->getInitialState();
        if (!empty($state)) {
            set($this, $state);
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
