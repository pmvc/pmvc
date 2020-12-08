<?php

$path = __DIR__.'/../vendor/autoload.php';
require $path;

if (!class_exists('PHPUnit_Framework_TestCase')) {
    class PHPUnit_Framework_TestCase extends
        \PHPUnit\Framework\TestCase
    {
    }
    class PHPUnit_Framework_Error extends Exception
    {
    }
}

\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../vendor/pmvc-plugin/']);
\PMVC\l(__DIR__.'/resources/FakePlugIn.php');
\PMVC\l(__DIR__.'/resources/FakeDebugPlugIn.php');
\PMVC\l(__DIR__.'/resources/FakeAlias.php');
\PMVC\l(__DIR__.'/resources/FakeDebugDump.php');
