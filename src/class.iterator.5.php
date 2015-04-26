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
 * ListIterator
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com> 
 * @license  http://opensource.org/licenses/MIT MIT 
 * @link     http://pear.php.net/package/PackageName
 */
class ListIterator extends Object implements \IteratorAggregate 
{
    protected $_values=array();

    public function getIterator() {
             return new ArrayIterator($this->_values);
    }

    public function __construct($values=array())
    {
            if (is_array($values)) {
                    $this->_values = &$values;
            }
    }
}
?>
