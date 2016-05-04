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
 * Alias.
 */
trait Alias
{
    public $defaultAlias;
    private $_aliasFunctions;

    /**
     * Custom is_callable for Alias.
     *
     * @param string $method Method
     * @param object $caller Caller
     *
     * @return mixed
     */
    public function isCallable($method, $caller = null)
    {
        $self = isArray($this) ? $this : ['this' => null];
        $func = false;
        if (!$this->_aliasFunctions) {
            $this->_aliasFunctions = $this->initAliasFunction();
        }
        if (is_null($caller)) {
            $caller = $self['this'] ?: $this;
        }
        foreach ($this->_aliasFunctions as $alias) {
            $func = $alias->get($this, $method, $caller);
            if (!empty($func)) {
                break;
            }
        }
        if (!$func) {
            if (isset($self['parent'])) {
                $parent = $self['parent'];
                if ($parent !== $self['this']
                    && is_callable([$parent, 'isCallable'])
                ) {
                    $func = $parent->isCallable($method, $caller);
                }
            }
        }

        return $func;
    }

    /**
     * Magic call for function alias.
     *
     * @param string $method method
     * @param array  $args   args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $func = $this->isCallable($method);
        if (empty($func)) {
            return !trigger_error(
                'Method not found: '.
                get_class($this).
                '::'.
                $method.
                '()'
            );
        } else {
            return call_user_func_array(
                $func,
                $args
            );
        }
    }

    /**
     * Init Alias.
     *
     * @return array
     */
    public function initAliasFunction()
    {
        return [
            'aliasClassConfig'  => AliasClassConfig::getInstance(),
            'aliasDefaultClass' => AliasDefaultClass::getInstance(),
            'aliasSrcFile'      => AliasSrcFile::getInstance(),
        ];
    }

    /**
     * SetDefaultAlias.
     *
     * @param object $obj class instance
     *
     * @return mixed
     */
    public function setDefaultAlias($obj)
    {
        $this->defaultAlias = $obj;
    }
}

/**
 * Abstract Alias Class.
 *
 * @category Alias
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
abstract class AbstractAlias
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     * @param object $caller Caller
     *
     * @return mixed
     */
    abstract public function get($self, $method, $caller);

    /**
     * Get Instance.
     *
     * @return object
     */
    public static function getInstance()
    {
        static $self;
        if (empty($self)) {
            $class = get_called_class();
            $self = new $class();
        }

        return $self;
    }
}

/**
 * Alias config.
 *
 * @category Alias
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class AliasClassConfig extends AbstractAlias
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     * @param object $caller Caller
     *
     * @return mixed
     */
    public function get($self, $method, $caller)
    {
        $func = false;
        if (is_callable($self[$method])) {
            $func = $self[$method];
        }

        return $func;
    }
}

/**
 * Alias default class.
 *
 * @category Alias
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class AliasDefaultClass extends AbstractAlias
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     * @param object $caller Caller
     *
     * @return mixed
     */
    public function get($self, $method, $caller)
    {
        $func = false;
        if (isset($self->defaultAlias)) {
            $func = [$self->defaultAlias, $method];
        }
        if (!is_callable($func)) {
            $func = false;
        }

        return $func;
    }
}

/**
 * Alias ./src/_xxx.php.
 *
 * @category Alias
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
class AliasSrcFile extends AbstractAlias
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     * @param object $caller Caller
     *
     * @return mixed
     */
    public function get($self, $method, $caller)
    {
        if (!is_callable([$self, 'getDir'])) {
            return false;
        }
        $path = $self->getDir().'src/_'.$method.'.php';
        if (!realpath($path)) {
            return false;
        }
        $r = l($path, _INIT_CONFIG);
        if (!isset($r->var[_INIT_CONFIG][_CLASS])) {
            return !trigger_error('Not defined default Class');
        } else {
            $class = $r->var[_INIT_CONFIG][_CLASS];
            if (!class_exists($class)) {
                return !trigger_error('Default Class not exits. ['.$class.']');
            }
            $func = new $class($caller);
            $func->caller = $caller;
        }
        if (!is_callable($func)) {
            return !trigger_error('Not implement __invoke function');
        }
        if (!isset($self[$method])) {
            $self[$method] = $func;
        }

        return $func;
    }
}
