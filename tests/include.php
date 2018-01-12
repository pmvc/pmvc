<?php

$path = __DIR__.'/../vendor/autoload.php';
include $path;

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class PHPUnit_Framework_TestCase extends
        \PHPUnit\Framework\TestCase
    {
    }
    class PHPUnit_Framework_Error extends
        \PHPUnit\Framework\Error\Error
    {
    }
}

\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../vendor/pmvc-plugin/']);
\PMVC\l(__DIR__.'/resources/FakePlug.php');
\PMVC\l(__DIR__.'/resources/FakeAlias.php');
\PMVC\l(__DIR__.'/resources/FakeDebugDump.php');
