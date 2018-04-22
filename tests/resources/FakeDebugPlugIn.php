<?php

namespace PMVC;

class FakeDebugPlugIn extends PlugIn
{
    function d()
    {
        if (is_callable($this['dCallback'])) {
            $args = func_get_args();
            call_user_func_array(
                $this['dCallback'],
                $args
            );
        }
    }
}
