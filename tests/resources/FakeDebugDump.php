<?php

namespace PMVC;

initPlugin(['debug' => null], true);

class FakeDebugDump extends PlugIn implements
    \PMVC\PlugIn\debug\DebugDumpInterface
{
    public function escape($s, $type = null)
    {
        return $s;
    }

    public function dump($p, $type = 'info')
    {
        var_dump($type);
    }
}
