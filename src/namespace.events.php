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
namespace PMVC\Event;

if (defined('\PMVC\Event\MAP_REQUEST')) {
    return;
}

const MAP_REQUEST = 'MapRequest';
const B4_PROCESS_ACTION = 'B4ProcessAction';
const B4_PROCESS_ERROR = 'B4ProcessError';
const B4_PROCESS_FORWARD = 'B4ProcessForward';
const B4_PROCESS_MAPPING = 'B4ProcessMapping';
const B4_PROCESS_VIEW = 'B4ProcessView';
const FINISH = 'Finish';
const SET_CONFIG = 'SetConfig';
