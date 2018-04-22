<?php

namespace PMVC;

class FakePlugIn extends PlugIn
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
