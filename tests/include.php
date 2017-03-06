<?php

namespace PMVC;

$path = __DIR__.'/../vendor/autoload.php';
include $path;
\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../vendor/pmvc-plugin/']);
l(__DIR__.'/resources/FakePlug.php');
l(__DIR__.'/resources/FakeAlias.php');
l(__DIR__.'/resources/FakeDebugDump.php');
