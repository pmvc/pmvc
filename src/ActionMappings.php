<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @version GIT: <git_id>
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
namespace PMVC;

/**
 * PMVC ActionMappings.
 *
 * @category CategoryName
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class ActionMappings
{
    /**
     * Mappings.
     *
     * @var array
     */
    private $_mappings;

    /**
     * Set mappings.
     *
     * @param array $mappings mappings
     *
     * @return bool
     */
    public function set($mappings)
    {
        $this->_mappings = $mappings;
        if (empty($this->_mappings)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Add mappings.
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
        $this->addByKey($mappings, ACTION_MAPPINGS);
        $this->addByKey($mappings, ACTION_FORMS);
        $this->addByKey($mappings, ACTION_FORWARDS);
    }

    /**
     * Add mappings by key.
     *
     * @param array  $mappings mappings
     * @param string $key      key
     *
     * @return array keys 
     */
    public function addByKey($mappings, $key)
    {
        if (!empty($mappings->{$key})) {
            $this->_mappings->{$key} = arrayMerge(
                $this->_mappings->{$key},
                $mappings->{$key}
            );
        }

        return $this->_mappings->{$key};
    }

    /**
     * Find an ActionMapping.
     *
     * @param string $name ActionMapping name
     *
     * @return ActionMapping
     */
    public function findMapping($name)
    {
        $mapping = &$this->_mappings->{ACTION_MAPPINGS}[$name];
        $mappingObj = new ActionMapping($mapping, $name);

        return $mappingObj;
    }

    /**
     * Find a form.
     *
     * @param string $name name
     *
     * @return ActionForm
     */
    public function findForm($name)
    {
        $form = &$this->_mappings->{ACTION_FORMS}[$name];
        if (is_callable($form[_CLASS])) {
            $actionForm = call_user_func($form[_CLASS]);
        } else {
            if (!class_exists($form[_CLASS])) {
                $form[_CLASS] = getOption(
                    _DEFAULT_FORM,
                    __NAMESPACE__.'\ActionForm'
                );
            }
            $actionForm = new $form[_CLASS]();
        }

        return $actionForm;
    }

    /**
     * Search for forward.
     *
     * @param string $name name
     *
     * @return ActionForward
     */
    public function findForward($name)
    {
        $forward = get($this->_mappings->{ACTION_FORWARDS}, $name);
        if ($forward) {
            return new ActionForward($forward);
        } else {
            return !trigger_error(
                'ActionForward not found: {'.$name.'} not exists',
                E_USER_WARNING
            );
        }
    }

    /**
     * Check if forward exists.
     *
     * @param string $name name
     *
     * @return bool
     */
    public function forwardExists($name)
    {
        return isset($this->_mappings->{ACTION_FORWARDS}[$name]);
    }

    /**
     * Check if action exists.
     *
     * @param string $name name
     *
     * @return bool
     */
    public function mappingExists($name)
    {
        return isset($this->_mappings->{ACTION_MAPPINGS}[$name]);
    }
}
