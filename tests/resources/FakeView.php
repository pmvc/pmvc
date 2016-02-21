<?php

namespace PMVC;

class FakeView extends PlugIn
{
    public $v = [];

    public function set($k, $v = null)
    {
        set($this->v, $k, $v);
    }

    public function setThemeFolder($v)
    {
    }

    public function setThemePath($v)
    {
    }

    public function process()
    {
        return $this;
    }
}
