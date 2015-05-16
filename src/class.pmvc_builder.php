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
 * PMVC MappingBuilder
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class MappingBuilder extends Object
{
    /**
     *	@var	array
     */
    private $_aaMap = array(
        ACTION_FORMS        => array()
        ,ACTION_MAPPINGS   => array()
        ,ACTION_FORWARDS   => array()
    );

    /**
     *	retrieve mappings
     *
     *	@return	ActionMappings
     */
    public function getMappings()
    {
        return (object)$this->_aaMap;
    }

    /**
     *  Add a form to mapping
     *
     * @param string $psFormId form id
     * @param array  $settings settings
     * 
     * @return bool
     */
    public function addForm($psFormId, $settings=array())
    {
        if (!isset($this->_aaMap[ACTION_FORMS][$psFormId])) {
            if (!isset($settings[_CLASS])) {
                $settings[_CLASS]=$psFormId;
            }
            $this->_aaMap[ACTION_FORMS][$psFormId][_CLASS] = $settings[_CLASS];
        }
    }

    /**
     * Get Form Default
     * 
     * @return array
     */
    public function getFormDefault()
    {
        return array(
            _CLASS=>null
        );
    }

    /**
     * Add filter 
     *
     * @param string $psFormId form id
     * @param string $psField  field id
     * @param array  $settings settings
     * 
     * @return bool
     */
    public function addFilter($psFormId, $psField, $settings)
    {
        $filter =&$this->_aaMap[ACTION_FORMS][$psFormId][_FILTER][$psField];
        $settings = mergeDefault(
            $this->getFilterDefault(), $settings
        );
        $type = (isset($settings[_TYPE]))?$settings[_TYPE]:null;
        unset($settings[_TYPE]);
        if ($type) {
            $filter[$type]=$settings;
        } else {
            $filter[]=$settings;
        }
        return true;
    }

    /**
     * Get Filter Default
     * 
     * @return array
     */
    public function getFilterDefault()
    {
        return array(
            _FUNCTION => null
            ,_OPTION  => null
            ,_TYPE    => null
        );
    }

    /**
     * Add global filter 
     *
     * @param string $psFormId form id
     * @param string $psField  field id
     * @param array  $settings settings
     * 
     * @return bool
     */
    public function addGlobalFilter($psFormId, $psField, $settings)
    {
        $settings[_TYPE]='g';
        return $this->addFilter(
            $psFormId, $psField, $settings
        );
    }

    /**
     * Add a Action to mapping
     *
     * @param string $psId     forward id
     * @param array  $settings settings
     * 
     * @return bool
     */
    public function addAction($psId, $settings)
    {
        $settings = mergeDefault(
            $this->getActionDefault(), $settings
        );
        if (!is_null($settings[_FORM])) {
            $this->addForm($settings[_FORM]);
        }
        $this->_aaMap[ACTION_MAPPINGS][$psId] = $settings;
        return true;
    }

    /**
     * Get Action Default
     * 
     * @return array
     */
    public function getActionDefault()
    {
        return array(
            _FUNCTION  => null
            ,_FORM     => null
            ,_VALIDATE => true
            ,_INPUT    => null
            ,_SCOPE    => 'request'
            ,_INITIAL  => null
            ,_TYPE     => null
            ,_CLEAN    => null
        );
    }


    /**
     * Add a forward to mapping
     *
     * @param string $psId     forward id
     * @param array  $settings settings
     * 
     * @return bool
     */
    public function addForward($psId, $settings)
    {
        $settings = mergeDefault(
            $this->getForwardDefault(), $settings
        );
        $this->_aaMap[ACTION_FORWARDS][$psId] = $settings;
        return true;
    }

    /**
     * Get forward default value
     * 
     * @return array
     */
    public function getForwardDefault()
    {
        return array(
            _PATH=>'_DEFAULT_'
            ,_TYPE=>'redirect'
            ,_INITIAL=>null
            ,_CLEAN=>null
            ,_HEADER=>null
            ,_LAZY_OUTPUT=>null
        );
    }
}
