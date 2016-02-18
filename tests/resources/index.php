<?php
$b = new PMVC\MappingBuilder();
${_INIT_CONFIG} = array(
    _CLASS => __NAMESPACE__.'\FakeAction',
    _INIT_BUILDER => $b
);

class FakeAction extends \PMVC\Action
{ 
}
