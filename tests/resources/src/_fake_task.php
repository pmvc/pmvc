<?php

namespace PMVC\UnitTest\Task;

use PMVC;

${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.'\_FakeTask';

class _faketask
{
    public function __invoke()
    {
        PMVC\option('set', 'd', 1);
    }
}
