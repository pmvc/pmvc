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
     * @param string $method method
     *
     * @return mixed
     */
    public function isCallAble($method)
    {
        $func = false;
        if (!$this->_aliasFunctions) {
            $this->_aliasFunctions = $this->initAliasFunction();
        }
        foreach ($this->_aliasFunctions as $alias) {
            $func = $alias->get($this, $method);
            if (!empty($func)) {
                break;
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
        $func = $this->isCallAble($method);
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
            'aliasClassConfig' => new AliasClassConfig(),
            'aliasDefaultClass' => new AliasDefaultClass(),
            'aliasSrcFile' => new AliasSrcFile(),
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
 * Alias Interface.
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
interface AliasInterface
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     *
     * @return mixed
     */
    public function get($self, $method);
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
class AliasClassConfig implements AliasInterface
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     *
     * @return mixed
     */
    public function get($self, $method)
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
class AliasDefaultClass implements AliasInterface
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     *
     * @return mixed
     */
    public function get($self, $method)
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
class AliasSrcFile implements AliasInterface
{
    /**
     * Get alias function.
     *
     * @param object $self   Same with object $this
     * @param string $method Call which funciton
     *
     * @return mixed
     */
    public function get($self, $method)
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
            $func = new $class();
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
