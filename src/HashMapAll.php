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
 * PMVC HashMapAll
 * What is overloading?
 * http://php.net/manual/en/language.oop5.overloading.php.
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
        foreach ($this->state as $sk=> $sv) {
            if (is_array($sv)) {
                $this->state[$sk] = new static($sv, true);
            }
        }

        return $this;
    }
}
