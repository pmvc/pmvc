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
 * PMVC Action
 *
 * @category CategoryName
 * @package  PackageName
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @link     http://pear.php.net/package/PackageName
 */
class ActionController
{
    /**
     * @var	ActionMappings
     */
     private $_mappings;
    /**
     * @var	HttpRequestServlet
     */
     private $_request;

    /**
     * ActionController construct with the options.
     *
     * @param array $options options
     */
    public function __construct($options=null)
    {
        $this->store(CONTROLLER, $this);
        if ($options) {
            $this->setOption($options);
        }
        $this->_request =& new Request();
    }

    /**
     * Set option
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function setOption($k, $v=null)
    {
        $this->store($k, $v);
        if (is_array($k)) {
            if (isset($k[_PLUGIN])) {
                initPlugIn($k[_PLUGIN]);
            }
        }
        call_plugin(
            'observer',
            'set',
            array(
                'setOption',
                $k
            )
        );
    }

    /**
     * store
     *
     * @param mixed $k key
     * @param mixed $v value
     *
     * @return void
     */
    public function store($k, $v=null)
    {
        option('set', $k, $v);
    }


    /**
     * Set mapping
     * 
     * @param mixed $mappings mappings
     * 
     * @return bool
     */
    public function setMapping($mappings)
    {
        if (!empty($mappings)) {
            $this->_mappings = new ActionMappings($mappings);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process the request.
     * 
     * @return mixed 
     */
    public function process()
    {
        $index  = $this->_processMapping();
        $result = $this->execute($index);
        $this->finish();
        if (!empty($result->slower)) {
            $this->execute($result->slower);
        }
        return $result;
    }

    /**
     * ActionMapping.
     *
     * @return	ActionMapping
     */
    private function _processMapping()
    {
        $index = getOPtion(RUN_ACTION);
        if (!$this->_mappings->mappingExists($index)) {
            if ($this->_mappings->mappingExists('index')) {
                $index = 'index';
            }
        }
        $this->store(RUN_ACTION, $index);
        return $index;
    }
        
    /**
     * execute mapping
     *
     * @param string $index pass run action
     * 
     * @return	ActionMapping
     */
    public function execute($index)
    {
        if (!$this->_mappings->mappingExists($index)) {
            trigger_error('No mappings found for index: '.$index);
        }
        $actionMapping = &$this->_mappings->findMapping($index);
        $actionForm = $this->_processForm($actionMapping);
        $actionForward = $this->_processAction(
            $actionMapping,
            $actionForm
        );
        if (is_object($actionForward)) {
            return $this->_processForward($actionForward);
        } else {
            return $actionForward;
        }
    }

    /**
     * ActionForm.
     *
     * @param array $actionMapping actionMapping
     * 
     * @return ActionForm
     */
    private function _processForm($actionMapping)
    {
        $actionForm = null;
        $name = $actionMapping->getForm();
        //verify that a form has been mapped
        if (strlen($name)) {
            $form =& $this->_mappings->findForm($name);
            $class = (!$form) ? $name : $form[_CLASS];
            if (!$class) {
                $class = 'ActionForm';
            }
            if (!class_exists($class)) {
                trigger_error(
                    'parse form error, not define class type', 
                    E_USER_ERROR
                );
            }
            //create and init form class
            $actionForm =& new $class();
        } else {
            $actionForm =& new ActionForm();
        }
        //add request parameters
        $this->initActionFormValue($actionForm, $actionMapping);

        //validate the form if necesarry
        if ($actionMapping->validate) {
            if (!$this->_processValidate($actionForm)) {
                $actionForm=false;
            }
        }
        return $actionForm;
    }

    /**
     * initActionFormValue
     *
     * @param object $actionForm    actionForm
     * @param object $actionMapping actionMapping
     * 
     * @return ActionForm
     */
    public function initActionFormValue($actionForm, $actionMapping)
    {
        $scope =& $actionMapping->scope;
        if (!is_array($scope)) {
            $scope = $this->_request->keySet();
        }
        foreach ($scope as $name) {
            $get = $this->_request->get($name);
            $actionForm->set($name, $get);
        }
    }

    /**
     * Call the validate() by ActionForm.
     *
     * @param ActionForm $actionForm actionForm
     *
     * @return boolean
     */
    private function _processValidate($actionForm)
    {
        return $actionForm->validate();
    }

    /**
     * Action for this request
     *
     * @param ActionMapping $actionMapping actionMapping
     * @param ActionForm    $actionForm    actionForm
     *
     * @return ActionForward
     */
    private function _processAction($actionMapping, $actionForm)
    {
        call_plugin(
            'observer', 
            'fire',
            array(
                'B4ProcessAction',
                true
            )
        );
        $class = $actionMapping->getClass();
        $func = $actionMapping->getFunc();
        if (!class_exists($class)) {
            trigger_error('parse action error, not define class type', E_USER_ERROR);
        }
        if (!method_exists($class, $func)) {
            $func = 'index';
        }
        return call_user_func_array(
            array(new $class, $func),
            array($actionMapping, $actionForm, $this->_request)
        );
    }

    /**
     * ActionForward
     *
     * @param ActionForward $actionForward actionForward
     * 
     * @return mixed 
     */
    private function _processForward($actionForward)
    {
        if ($actionForward->clean) {
            $actionForward->clean();
        }
        return $actionForward->go();
    }

    /**
     * getAppFile
     *
     * @param string $defaultAppFolder defaultAppFolder
     * 
     * @return mixed 
     */
    public function getAppFile($defaultAppFolder=null)
    {
        call_plugin(
            'observer',
            'fire',
            array(
                'MapRequest'
                ,true
            )
        );
        $file = $this->processApp(
            getOption(RUN_APP),
            $defaultAppFolder,
            true
        );
        if (!is_int($file)) {
            call_plugin(
                'observer',
                'fire',
                array(
                    'GetAppFile'
                    ,true
                )
            );
            return $file;
        } else {
            return null;
        }
    }

    /**
     * processApp
     *
     * @param string $appName          appName
     * @param string $defaultAppFolder defaultAppFolder
     * @param string $welcome          welcome
     * 
     * @return mixed 
     */
    protected function processApp($appName, $defaultAppFolder=null, $welcome=false)
    {
        $file = null;
        if ($appName) {
            $defaultAppFolder=realpath($defaultAppFolder);
            if ($defaultAppFolder) {
                $defaultAppFolder=lastSlash($defaultAppFolder);
                $this->store(RUN_APP_FOLDER, $defaultAppFolder);
                $file = $defaultAppFolder.$appName.'/index.php';
                $file = realpath($file);
            }
        }
        if ($file) {
            return $file;
        } elseif ($welcome) {
            $welcome = getOption(WELCOME_APP);
            return $this->processApp($welcome, $defaultAppFolder);
        } else {
            return 2;
        }
    }
        

    /**
     * Finish off the request and take down the controller.
     * 
     * @return void
     */
    public function finish()
    {
        call_plugin(
            'observer', 
            'fire', 
            array(
                'Finish'
                ,true
            )
        );
    }

    /**
     * getRequest
     * 
     * @return mixed 
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * getMapping
     * 
     * @return mixed 
     */
    public function getMapping()
    {
        return $this->_mappings;
    }
}
