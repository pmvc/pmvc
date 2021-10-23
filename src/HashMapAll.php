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

/**
 * HashMapAll.
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
class HashMapAll extends HashMap
{
    /**
     * Set.
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return bool
     */
    public function offsetSet($k, $v)
    {
        parent::offsetSet($k, $v);
        $keys = toArray($this->lastKeys());
        foreach ($keys as $k) {
            if (is_array($this->state[$k])) {
                $this->state[$k] = new static($this->state[$k], true);
            }
        }

        return $this;
    }
}
