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
    private $_mappings = array();

    /**
     * Set mappings
     *
     * @param array $mappings mappings
     *
     * @return void
     */
    public function setMappings($mappings)
    {
        $this->_mappings =& $mappings;
    }

    /**
     * Find an ActionMapping
     *
     * @param string &$path $path
     *
     * @return ActionMapping
     */
    public function &findMapping(&$path)
    {
        $mapping =& $this->_mappings->__action_mappings__[$path];
        $mappingObj = new ActionMapping($mapping, $path);
        return $mappingObj;
    }

    /**
     * Find a form
     *
     * @param string $name name
     *
     * @return array
     */
    public function &findForm($name)
    {
        return get($this->_mappings->__action_forms__, $name);
    }

    /**
     * Search for the forward
     *
     * @param string $name name
     * 
     * @return string
     */
    public function findForward($name)
    {
        return get($this->_mappings->__action_forwards__, $name);
    }

    /**
     * Check to see if a action exists.
     *
     * @param string $name name
     *
     * @return boolean
     */
    public function mappingExists($name)
    {
        return isset($this->_mappings->__action_mappings__[$name]);
    }
}
