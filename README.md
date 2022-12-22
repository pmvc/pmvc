## Table of Contents
- [PMVC](#pmvc)
- [Getting Started](#getting-started)
- [How to use?](#how-to-use)
  - [Plugin hello world](#plugin-hello-world)
  - [PlugIn features](#plugin-features)
  - [PlugIn Generator](#plugin-generator)
- [[M]odel [V]iew [C]ontroller](#mvc)
- [PMVC Family](#pmvc-family)
- [Explain addPlugInFolders](#explain-addpluginfolders)
- [Quick test](#quick-test)

<img src="https://raw.githubusercontent.com/pmvc/pmvc.github.io/master/flow5.png">

PMVC
===============
A simple MVC for unidirectional dataflow architecture.

[![Latest Stable Version](https://poser.pugx.org/pmvc/pmvc/v/stable)](https://packagist.org/packages/pmvc/pmvc) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc/pmvc/v/unstable)](https://packagist.org/packages/pmvc/pmvc) 
[![CircleCI](https://circleci.com/gh/pmvc/pmvc/tree/main.svg?style=svg)](https://circleci.com/gh/pmvc/pmvc/tree/main)
[![StyleCI](https://styleci.io/repos/34601083/shield)](https://styleci.io/repos/34601083)
[![Coverage Status](https://coveralls.io/repos/github/pmvc/pmvc/badge.svg?branch=main)](https://coveralls.io/github/pmvc/pmvc?branch=main)
[![License](https://poser.pugx.org/pmvc/pmvc/license)](https://packagist.org/packages/pmvc/pmvc)
[![Total Downloads](https://poser.pugx.org/pmvc/pmvc/downloads)](https://packagist.org/packages/pmvc/pmvc) 

## Getting Started
* https://github.com/pmvc/pmvc/wiki
* Heroku (or Dokku) ready app test 
   * [![Deploy](https://www.herokucdn.com/deploy/button.png)](https://github.com/pmvc/react-pmvc)

## How to use?
```
plug( 'PluginName', ['option'] );
```
### Plugin Hello world
   * Source Code
```
<?php
namespace PMVC\PlugIn\hello_world;
${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\hello_world';
class hello_world extends \PMVC\PlugIn
{
    public function say()
    {
        echo $this[0]."\n";
    }
}
```
   * Used
```
\PMVC\plug('hello_world', ['Hello, PMVC.'])->say();
```

### PlugIn features
   * addPlugInFolders
   * callPlugin
   * unPlug
   * rePlug
   * initPlugIn
   * plug

### PlugIn Generator
   * https://github.com/pmvc/generator-pmvc

## MVC
   * Controller
      * https://github.com/pmvc-plugin/controller
   * View
      * https://github.com/search?q=topic:pmvc-view
      * view_react
      * view_html
      * view_json
      * view_cli
   * Model
      * https://github.com/pmvc-plugin
   * MVC Demo Project
      * https://github.com/pmvc/react-pmvc

## PMVC Family
   * Core Library
      * https://github.com/pmvc/pmvc
      * CLI
         * https://github.com/pmvc/pmvc-cli
   * PMVC Plug-ins
      * https://github.com/pmvc-plugin
   * PMVC Applications
      * https://github.com/pmvc-app
   * PMVC Themes
      * https://github.com/pmvc-theme

## Explain addPlugInFolders
The last folder will have more high priority.


Such as 
```
addPlugInFolders(['./a', './b'])
```
If folder a and folder b both have same plugin will apply with folder b.

---


## Quick test
```
composer require pmvc/pmvc
```
### Quick test with docker
```
docker run --rm -i hillliu/pmvc-phpunit composer require pmvc/pmvc
```

## phpunit Docker
* https://github.com/pmvc/docker-pmvc-phpunit

MIT 2022
