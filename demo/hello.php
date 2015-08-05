<?php
include_once('../vendor/autoload.php');
PMVC\Load::mvc();

#cache
PMVC\plug('cache_header')->nocache();

$build = new PMVC\MappingBuilder();
$build->addAction('index', array(
    _FUNCTION=>function(){
        return 'Hello World!';
    }
));

$a = (new PMVC\ActionController())->process( $build->getMappings() );
var_dump($a);

