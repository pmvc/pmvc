<?php

namespace PMVC;

class FakeAlias extends PlugIn
{
    public function init()
    {
        $this->setDefaultAlias(new FakeObject());
        $this->parentAlias = plug('fake');
    }

    public function getDir()
    {
        $e = getOption('e');
        option('set', 'e', $e+1);
        return __DIR__.'/';
    }
}

class FakeAliasChild extends FakeAlias
{
    public function getDir()
    {
        return null;
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
        $e = getOption('e');
        option('set', 'e', $e+1);
        return __DIR__.'/';
    }
}

class FakeAliasWithoutArrayAccessChild extends FakeAliasWithoutArrayAccess
{
    public function __construct()
    {
        parent::__construct();
        $this->parentAlias = new FakeAliasWithoutArrayAccess();
    }

    public function getDir()
    {
        return null;
    }
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
