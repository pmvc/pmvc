<?php

namespace PMVC;

$path = __DIR__.'/../vendor/autoload.php';
include $path;

\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../vendor/pmvc-plugin/']);
\PMVC\l(__DIR__.'/resources/FakePlug.php');
\PMVC\l(__DIR__.'/resources/FakeAlias.php');
\PMVC\l(__DIR__.'/resources/FakeDebugDump.php');
