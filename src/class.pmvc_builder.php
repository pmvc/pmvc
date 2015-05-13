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
    var $_aaMap = array(
        ACTION_FORMS	    => array()
        ,ACTION_MAPPINGS   => array()
        ,ACTION_FORWARDS   => array()
    );

    /**
     *	retrieve mappings
     *
     *	@return	ActionMappings
     */
    function getMappings()
    {
        return (object)$this->_aaMap;
    }

    /**
     *  Add a form to mapping
     */
    function addForm($psFormId, $settings=array())
    {
        if(!isset($this->_aaMap[ACTION_FORMS][$psFormId])){
            if(!isset($settings[_CLASS])){
                $settings[_CLASS]=$psFormId;
            }
            $this->_aaMap[ACTION_FORMS][$psFormId][_CLASS] = $settings[_CLASS];
        }
    }
    function getFormDefault(){
        return array(
            _CLASS=>null
        ); 
    }


    function addFilter($psFormId, $psField, $settings)
    {
        
        $filter =&$this->_aaMap[ACTION_FORMS][$psFormId][_FILTER][$psField]; 
        $settings = mergeDefault(
            $this->getFilterDefault()
            ,$settings
        );
        $type = (isset($settings[_TYPE]))?$settings[_TYPE]:null;
        unset($settings[_TYPE]);
        if($type){
            $filter[$type]=$settings;
        }else{
            $filter[]=$settings;
        }
        return true;
    }

    function getFilterDefault(){
        return array(
            _FUNCTION => null 
            ,_OPTION  => null 
            ,_TYPE    => null
        );
    }

    function addGlobalFilter($psFormId, $psField, $settings)
    {
        $settings[_TYPE]='g'; 
        return $this->addFilter(
            $psFormId
            ,$psField
            ,$settings
        );
    }

    function addAction($psId,$settings)
    {
        $settings = mergeDefault(
            $this->getActionDefault()
            ,$settings
        );
        if(!is_null($settings[_FORM])){
            $this->addForm($settings[_FORM]);
        }
        $this->_aaMap[ACTION_MAPPINGS][$psId] = $settings;
        return true;
    }

    function getActionDefault(){
        return array(
            _CLASS	=> null 
            ,_FORM	=> null 
            ,_VALIDATE  => true 
            ,_INPUT	=> null
            ,_SCOPE	=> 'request' 
            ,_INITIAL   => null
            ,_TYPE      => null
            ,_FUNCTION  => null
            ,_CLEAN     => null
        );
    }


    /**
     *	Add a forward to mapping
     */
    function addForward($psId, $settings )
    {
        $settings = mergeDefault(
            $this->getForwardDefault()
            ,$settings
        );
        $this->_aaMap[ACTION_FORWARDS][$psId] = $settings;
        return true;
    }

    function getForwardDefault(){
        return array(
            _PATH=>'_DEFAULT_'
            ,_TYPE=>'redirect'
            ,_INITIAL=>null
            ,_CLEAN=>null
            ,_HEADER=>null
            ,_SLOWER=>null
        );
    }
}
?>
