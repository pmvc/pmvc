<?php

namespace PMVC;

class FakePlug extends PlugIn
{
    public function onTest()
    {
        \PMVC\option('set', 'test', 'ontest');
    }
}
