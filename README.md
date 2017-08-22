## Table of Contents
- [PMVC](#pmvc)
- [Getting Started](#getting-started)
- [How to use?](#how-to-use)
  - [Plugin hello world](#plugin-hello-world)
  - [PlugIn features](#plugin-features)
  - [PlugIn Generator](#plugin-generator)
- [[M]odel [V]iew [C]ontroller](#mvc)
- [PMVC Family](#pmvc-family)
- [PHP Support Version](#php-version)

<img src="https://raw.githubusercontent.com/pmvc/pmvc.github.io/master/flow5.png">

PMVC
===============
A simple MVC for unidirectional dataflow architecture.

[![Latest Stable Version](https://poser.pugx.org/pmvc/pmvc/v/stable)](https://packagist.org/packages/pmvc/pmvc) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc/pmvc/v/unstable)](https://packagist.org/packages/pmvc/pmvc) 
[![Build Status](https://travis-ci.org/pmvc/pmvc.svg?branch=master)](https://travis-ci.org/pmvc/pmvc)
[![StyleCI](https://styleci.io/repos/34601083/shield)](https://styleci.io/repos/34601083)
[![Coverage Status](https://coveralls.io/repos/github/pmvc/pmvc/badge.svg?branch=master)](https://coveralls.io/github/pmvc/pmvc?branch=master)
[![License](https://poser.pugx.org/pmvc/pmvc/license)](https://packagist.org/packages/pmvc/pmvc)
[![Total Downloads](https://poser.pugx.org/pmvc/pmvc/downloads)](https://packagist.org/packages/pmvc/pmvc) 

## Getting Started
* https://github.com/pmvc/pmvc/wiki
* Heroku (or Dokku) ready app test 
   * [![Deploy](https://www.herokucdn.com/deploy/button.png)](https://github.com/pmvc/react-pmvc)

## How to use?
```
plug( 'plugin name', ['option'] );
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
   * https://github.com/pmvc/generator-php-pmvc-plugin

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
   * PMVC Plug-ins
      * https://github.com/pmvc-plugin
   * PMVC Applications
      * https://github.com/pmvc-app
   * PMVC Themes
      * https://github.com/pmvc-theme

## PHP Version
 * HHVM Support
![tested](http://php-eye.com/badge/pmvc/pmvc/tested.svg)
