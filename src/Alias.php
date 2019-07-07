<?php
/**
 * PMVC.
 *
 * PHP version 5
 *
 * @category Alias
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
trait Alias
{
    public $defaultAlias;
    public $parentAlias;
    private $_typeOfAlias;

    /**
     * Custom is_callable for Alias.
     *
     * @param string $method Method
     *
     * @return mixed
     */
    public function isCallable($method)
    {
        if (method_exists($this, $method)) {
            return [$this, $method];
        }
        $func = false;
        if (!$this->_typeOfAlias) {
            $this->_typeOfAlias = $this->getTypeOfAlias();
        }
        $caller = get($this, THIS, $this);
        foreach ($this->_typeOfAlias as $alias) {
            $func = $alias->get($this, $method, $caller);
            if (!empty($func)) {
                break;
            }
        }
        if (!$func) {
            if (!empty($this->parentAlias)) {
                $parent = $this->parentAlias;
                if (spl_object_hash($parent) !== spl_object_hash($caller)
                    && is_callable([$parent, 'isCallable'])
                ) {
                    $func = $parent->isCallable($method);
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
                str_replace('\\\\', '\\', get_class($this)).'::'.
                $method.'()'.
                '. Please check alias file already use lowercase.'
            );
        } else {
            return call_user_func_array(
                $func,
                $args
            );
        }
    }

    /**
     * Get type of alias.
     *
     * @return array
     */
    protected function getTypeOfAlias()
    {
        return [
            'aliasAsKey'     => AliasAsKey::getInstance(),
            'aliasSrcFile'   => AliasSrcFile::getInstance(),
            'aliasAsDefault' => AliasAsDefault::getInstance(),
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
 * Forward method call to ArrayClass key, such as $this['xxx'].
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
class AliasAsKey extends AbstractAlias
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
        $func = get($self, strtolower($method));
        if (is_callable($func)) {
            return $func;
        } else {
            return false;
        }
    }
}

/**
 * Alias undefined function to another class.
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
class AliasAsDefault extends AbstractAlias
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
        if (!method_exists($self, 'getDir')) {
            return false;
        }
        $lowerMethod = strtolower($method);
        $path = $this->_getPath($self, $lowerMethod);
        if (!realpath($path)) {
            $path = $this->_getPath($self, camelCase($method, '_'));
            if (!realpath($path)) {
                return false;
            }
        }
        $r = l($path, _INIT_CONFIG);
        $class = value($r, ['var', _INIT_CONFIG, _CLASS]);
        if (!$class) {
            return !trigger_error('Not defined default Class. ['.$path.']');
        } else {
            if (!class_exists($class)) {
                return !trigger_error('Default class not exists. ['.$class.']');
            }
            $func = new $class($caller);
            $func->caller = $caller;
        }
        if (!is_callable($func)) {
            return triggerJson(
                'Not implement __invoke function', [
                'path' => $path, 
                'class'=> $class, 
                'method'=>$method]
            );
        }
        if (isArray($self) && !isset($self[$lowerMethod])) {
            $self[$lowerMethod] = $func;
        } elseif (!isset($self->{$lowerMethod})) {
            $self->{$lowerMethod} = $func;
        }

        return $func;
    }

    /**
     * Get alias file path.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     *
     * @return string
     */
    private function _getPath($self, $method)
    {
        return $self->getDir().'src/_'.$method.'.php';
    }
}
