<?php

namespace PMVC;

class FakeAlias extends PlugIn
{
    public function init()
    {
        $this->setDefaultAlias(new FakeObject());
        $this['parent'] = plug('fake');
    }

    public function getDir()
    {
        return __DIR__.'/';
    }
}

class FakeAliasWithoutArrayAccess
{
    use Alias;
    public function __construct()
    {
        $this->setDefaultAlias(new FakeObject());
    }

    public function getDir()
    {
        return __DIR__.'/';
    }
}

class FakeAliasChild extends FakeAlias
{
}

class FakeObject
{
    public function a()
    {
        option('set', 'a', 1);
    }

    public function b()
    {
        echo 'b';
    }
}

class FakeInvoke
{
    public function __invoke()
    {
        option('set', 'c', 1);
    }
}
