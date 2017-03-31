<?php

namespace PMVC;

class FakePlug extends PlugIn
{
    public function onTest()
    {
        return true;
    }

    public function __tostring()
    {
        return __CLASS__;
    }
}
