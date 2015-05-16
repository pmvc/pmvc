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
 * PMVC ActionMappings
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com> 
 * @license  http://opensource.org/licenses/MIT MIT 
 * @link     http://pear.php.net/package/PackageName
 */
class ActionMappings
{
    /**
     * @var array
     */
    var $_mappings;

    /**
     * ActionMappings
     */
    function __construct($mappings)
    {
        $this->_mappings =& $mappings;
    }


    /**
     * Find an ActionMapping
     * @access public
     * @param string $path
     * @return ActionMapping
     */
    function &findMapping(&$path)
    {
        $mapping =& $this->_mappings->__action_mappings__[$path];
        $mappingObj = new ActionMapping($mapping,$path);
        return $mappingObj;
    }

    /**
     * Find a form
     * @access public
     * @param string $name
     * @return array
     */
    function &findForm($name)
    {
        return get($this->_mappings->__action_forms__,$name);
    }

    /**
     * Search for the forward
     * @access public
     * @return string
     */
    function findForward($name)
    {
        return get($this->_mappings->__action_forwards__,$name);
    }

    /**
     * Check to see if a action exists.
     * @access public
     * @param string $name
     * @return boolean
     */
    function mappingExists($name)
    {
        return isset($this->_mappings->__action_mappings__[$name]);
    }
}
?>
