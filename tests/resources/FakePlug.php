<?php

namespace PMVC;

class FakePlug extends PlugIn
{
    function onTest()
    {
        \PMVC\option('set','test','ontest');
    }
}
