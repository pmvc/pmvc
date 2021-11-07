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
    public $preCookFunctionName;
    public $aliasFileFilter;
    public $aliasFileMapping;
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
        if (is_string($method)) {
            if (method_exists($this, $method)) {
                return [$this, $method];
            }
            $method = strtolower($method);
            if (is_callable($this->preCookFunctionName)) {
                $method = call_user_func($this->preCookFunctionName, $method);
            }
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
                'Method not found: "'.
                str_replace('\\\\', '\\', get_class($this)).'::'.
                $method.'()"'.
                '. Please confirm alias file already use lowercase.'
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

    /**
     * Cache function.
     *
     * @param string   $method Alias method name.
     * @param callable $func   Callable function.
     *
     * @return void
     */
    public function setCallableToAttribute($method, $func)
    {
        if (isArray($this) && !isset($this[$method])) {
            $this[$method] = $func;
        } elseif (!isset($this->{$method})) {
            $this->{$method} = $func;
        }
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
        $func = get($self, $method);
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
            $classes = is_array($self->defaultAlias) ?
            $self->defaultAlias :
            [$self->defaultAlias];
            foreach ($classes as $c) {
                if (method_exists($c, $method)) {
                    $func = [$c, $method];
                    $self->setCallableToAttribute($method, $func);

                    return $func;
                }
            }
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
        $path = $this->_getPath($self, $method);
        $r = l($path, _INIT_CONFIG, ['ignore'=> true]);
        if (!$r) {
            return false;
        }
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
                'Not implement __invoke function',
                [
                    'path'  => $path,
                    'class' => $class,
                    'method'=> $method, ]
            );
        }
        $self->setCallableToAttribute($method, $func);

        return $func;
    }

    /**
     * Get file mapping.
     *
     * @param string   $path   Folder path.
     * @param callable $filter Callable filter.
     *
     * @return string
     */
    private function _getFileMapping($path, $filter = true)
    {
        $mapArr = [];
        $files = glob($path);
        if (true === $filter) {
            $filter = function ($f) {
                return strtolower(str_replace('_', '', $f));
            };
        }
        foreach ($files as $fPath) {
            $fName = basename($fPath);
            $fName = $filter(substr($fName, 1, strlen($fName) - 5));
            if (empty($fName)) {
                return !trigger_error(
                    'aliasFileFilter not setup correct.'
                );
            }
            $mapArr[$fName] = $fPath;
        }

        return $mapArr;
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
        $path = $self->getDir().'src/_';
        if ($self->aliasFileFilter) {
            if (empty($self->aliasFileMapping)) {
                $self->aliasFileMapping
                    = $this->_getFileMapping($path.'*.php', $self->aliasFileFilter);
            }
            $map = $self->aliasFileMapping;
            if (isset($map[$method])) {
                return $map[$method];
            }
        }

        return $path.$method;
    }
}
