<?php

namespace PMVC\plugin2;

use PMVC\PlugIn;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\testplugin';

class testplugin extends PlugIn
{
    public function init()
    {
        $this['test'] = 'plugin2';
    }
}
