<?php

namespace PMVC\UnitTest\Task;

use PMVC;

${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.'\_FakeTask';

class _faketask
{
    public $caller;
    public function __invoke($v = 1)
    {
        PMVC\option('set', 'foo', $v);

        return $this;
    }
}
