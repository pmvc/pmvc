<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 *
 * @version  GIT: <git_id>
 *
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC ActionMapping.
 *
 * @category CategoryName
 *
 * @author   Hill <hill@kimo.com>
 * @license  http://opensource.org/licenses/MIT MIT
 *
 * @link     https://packagist.org/packages/pmvc/pmvc
 */
class ActionMapping extends HashMap
{
    /**
     * Func.
     *
     * @var string
     */
    public $func;

    /**
     * Form.
     *
     * @var string
     */
    public $form;

    /**
     * Validate.
     *
     * @see ActionController::_processForm
     *
     * @var bool
     */
    public $validate = true;

    /**
     * Set request scope , post or get.
     *
     * @see ActionController::initActionFormValue
     *
     * @var string
     */
    public $scope;

    /**
     * This action mapping name.
     *
     * @var string
     */
    public $name;

    /**
     * ActionMapping.
     *
     * @param array  $mapping mapping
     * @param string $name    name
     */
    public function __construct(&$mapping, $name)
    {
        $this->name = $name;
        if (isset($mapping[_FUNCTION])) {
            $this->func = $mapping[_FUNCTION];
        }
        if (isset($mapping[_FORM])) {
            $this->form = $mapping[_FORM];
        } else {
            $this->form = __NAMESPACE__.'\ActionForm';
        }
        if (isset($mapping[_VALIDATE])) {
            $this->validate = $mapping[_VALIDATE];
        }
        if (isset($mapping[_SCOPE])) {
            $this->scope = $mapping[_SCOPE];
        }
    }

    /**
     * Check ActionForwards exists.
     *
     * @param array $name name
     *
     * @return mixed
     */
    public function offsetExists($name)
    {
        return getC()->getMapping()->forwardExists($name);
    }

    /**
     * Get ActionForwards from ActionMapping.
     *
     * @param array $name name
     *
     * @return mixed
     */
    public function offsetGet($name)
    {
        return getC()->getMapping()->findForward($name);
    }
}
