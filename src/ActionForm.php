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
 * PMVC ActionForm.
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
class ActionForm extends HashMap
{
    /**
     * Let ActionForm coule be a separate file unser src.
     *
     * @param array $state state
     *
     * @return this
     */
    public function &__invoke($state = null)
    {
        return $this;
    }

    /**
     * Validate.
     *
     * @return mixed
     */
    public function validate()
    {
        return true;
    }
}
