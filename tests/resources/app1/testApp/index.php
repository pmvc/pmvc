<?php

$b = new PMVC\MappingBuilder();
${_INIT_CONFIG} = [
    _CLASS        => __NAMESPACE__.'\FakeAction',
    _INIT_BUILDER => $b,
];

class FakeAction extends \PMVC\Action
{
    public function init()
    {
        \PMVC\getC()->store('test', 'app1');
    }
}
