<?php

$path = __DIR__.'/../vendor/autoload.php';
require $path;

\PMVC\Load::plug(['unit' => null], [__DIR__.'/../vendor/pmvc-plugin/']);

\PMVC\l(__DIR__.'/../tests/resources/FakePlugIn');
\PMVC\l(__DIR__.'/../tests/resources/FakeDebugPlugIn');
\PMVC\l(__DIR__.'/../tests/resources/FakeAlias');
\PMVC\l(__DIR__.'/../tests/resources/FakeDebugDump');
