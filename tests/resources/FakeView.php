<?php
namespace PMVC;
class FakeView extends PlugIn
{
    public $v = array();
    function set($k,$v=null){
        set($this->v,$k,$v);
    }
    function setThemeFolder($v){

    }
    function setThemePath($v){

    }
    function process(){
        return $this;
    }
}
