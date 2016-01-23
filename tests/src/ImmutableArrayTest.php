<?php
namespace PMVC;
class ImmutableArrayTest extends \PHPUnit_Framework_TestCase
{
    function testAutoIncreateSize()
    {
        $a = array(1,2);
        $imtb = new mockImmutableArray($a);
        $state = $imtb->getState();
        $this->assertEquals($state->getSize(),2);
        $imtb['3'] = 'aaa';
        $this->assertEquals($state->getSize(),3);
    }
}

class mockImmutableArray extends ImmutableArray
{
    function getState()
    {
        return $this->state;
    }
}
