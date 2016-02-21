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
 * Router Interface.
 * 
 * @category Default
 *
 * @package PMVC
 *
 * @author  Hill <hill@kimo.com>
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link https://packagist.org/packages/pmvc/pmvc
 */
interface RouterInterface
{
    /**
     * Build command.
     * 
     * @param string $path   path
     * @param array  $params params
     * 
     * @return void
     */
    public function buildCommand($path, $params);

    /**
     * Process Header.
     * 
     * @param array $headers headers
     * 
     * @return void
     */
    public function processHeader($headers);

    /**
     * Execute another program.
     *
     * @param string $path path
     * 
     * @return mixed
     */
    public function go($path);
}
