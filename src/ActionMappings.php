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
 * @link     https://packagist.org/packages/pmvc/pmvc 
 */
namespace PMVC;

/**
 * PMVC ActionMappings
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     https://packagist.org/packages/pmvc/pmvc 
 */
class ActionMappings
{
    /**
     * Mappings
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
    public function set($mappings)
    {
        $this->_mappings =& $mappings;
    }

    /**
     * Add mappings
     *
     * @param array $mappings mappings
     *
     * @return void
     */
    public function add($mappings)
    {
        if (empty($this->_mappings)) {
            return $this->set($mappings);
        }
        $this->addMappingByKey($mappings, ACTION_MAPPINGS);
        $this->addMappingByKey($mappings, ACTION_FORMS);
        $this->addMappingByKey($mappings, ACTION_FORWARDS);
    }

    /**
     * Add mappings by key
     *
     * @param array  $mappings mappings
     * @param string $key      key 
     *
     * @return void
     */
    public function addMappingByKey($mappings, $key)
    {
        if (!empty($mappings->{$key})) {
            $this->_mappings->{$key} = array_merge(
                $this->_mappings->{$key},
                $mappings->{$key}
            );
        }
    }

    /**
     * Find an ActionMapping
     *
     * @param string $path path
     *
     * @return ActionMapping
     */
    public function findMapping($path)
    {
        $mapping =& $this->_mappings->{ACTION_MAPPINGS}[$path];
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
        return get($this->_mappings->{ACTION_FORMS}, $name);
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
        return get($this->_mappings->{ACTION_FORWARDS}, $name);
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
        return isset($this->_mappings->{ACTION_MAPPINGS}[$name]);
    }
}
