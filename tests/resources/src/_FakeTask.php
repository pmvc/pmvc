<?php
namespace PMVC\UnitTest\Task;
use PMVC;
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\fake_task';

class fake_task
{
    function __invoke()
    {
        PMVC\option('set','d',1);
    }
}
