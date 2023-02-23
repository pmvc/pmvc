<?php

$path = __DIR__ . '/../vendor/autoload.php';
require $path;

\PMVC\Load::plug(['unit' => null], [__DIR__ . '/../vendor/pmvc-plugin/']);

\PMVC\l(__DIR__ . '/../src/Constants.php');
\PMVC\l(__DIR__ . '/resources/FakePlugIn');
\PMVC\l(__DIR__ . '/resources/FakeDebugPlugIn');
\PMVC\l(__DIR__ . '/resources/FakeAlias');
\PMVC\l(__DIR__ . '/resources/FakeDebugDump');
