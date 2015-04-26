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
 * PMVC HashMap
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com> 
 * @license  http://opensource.org/licenses/MIT MIT 
 * @link     http://pear.php.net/package/PackageName
 */
class HashMap extends ListIterator 
{
    function containsKey($key)
    {
        return array_key_exists($key, $this->_values);
    }

    function containsValue($value)
    {
        return in_array($value, $this->_values);
    }

    function keySet()
    {
        return array_keys($this->_values);
    }

    function get(...$p){
        return get($this->_values,...$p);
    }

    function set(...$p){
        return set($this->_values,...$p);
    }

    function clean(...$p){
        return clean($this->_values,...$p);
    }
}
?>
