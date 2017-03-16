<?php

namespace PMVC\UnitTest\Task;

use PMVC;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\WithWrongName';

class With_Wrong_Name 
{
    public function __invoke()
    {
    }
}
