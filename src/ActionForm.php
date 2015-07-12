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
 * PMVC ActionForm
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT 
 * @link     http://pear.php.net/package/PackageName
 */
class ActionForm extends HashMap
{

    /**
     * Option
     */
     public $options;

    /**
     * Construct
     *
     * @param array $options values
     *
     * @return ArrayIterator
     */
    public function __construct($options=array())
    {
        if (is_array($options)) {
            $this->options = &$options;
        }
    }

    /**
     * Validate
     * 
     * @return mixed 
     */
    public function validate()
    {
        return true;
    }
}
?>
