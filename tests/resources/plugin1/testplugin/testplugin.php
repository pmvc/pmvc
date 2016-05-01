<?php

namespace PMVC\plugin1;

use PMVC\PlugIn;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\testplugin';

class testplugin extends PlugIn
{
    public function init()
    {
        $this['test'] = 'plugin1';
    }
}
