<?php

namespace PMVC;

require __DIR__.'/../include.php';
$path = __DIR__.'/../vendor/autoload.php';
include $path;
l(__DIR__.'/resources/FakePlug.php');
l(__DIR__.'/resources/FakeAlias.php');
\PMVC\Load::plug();
\PMVC\addPlugInFolders([__DIR__.'/../../']);
