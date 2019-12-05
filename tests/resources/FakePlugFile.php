<?php

namespace PMVC;

use SplSubject;

${_INIT_CONFIG
}['update'] = 1;
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\FakePlugFile';

class FakePlugFile extends PlugIn
{
    public function init()
    {
        $this['init'] = 1;
    }

    public function update(SplSubject $sub = null)
    {
        $this['update']++;

        return parent::update($sub);
    }
}
