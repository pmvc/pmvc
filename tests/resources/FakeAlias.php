<?php
namespace PMVC;
class FakeAlias extends PlugIn
{
    function init ()
    {
        $this->setDefaultAlias(new FakeObject);
    }
    function getDir()
    {
        return __DIR__.'/';
    }
}

class FakeObject
{
    function a()
    {
        option('set', 'a', 1);
    }

    function b()
    {
        echo 'b';
    }
}

class FakeInvoke
{
    function __invoke()
    {
        option('set', 'c', 1);
    }
}
