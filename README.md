## Table of Contents
- [PMVC](#pmvc)
- [Getting Started](#getting-started)
- [How to use?](#how-to-use)
  - [Plugin hello world](#plugin-hello-world)
  - [PlugIn features](#plugin-features)
- [MVC](#mvc)
- [PMVC Family](#pmvc-family)
- [HHVM Support](#hhvm-support)
- [PHP Version](#php-version)

<img src="https://raw.githubusercontent.com/pmvc/pmvc.github.io/master/flow5.png">

PMVC
===============
A simple MVC for unidirectional dataflow architecture.

[![Latest Stable Version](https://poser.pugx.org/pmvc/pmvc/v/stable)](https://packagist.org/packages/pmvc/pmvc) 
[![Latest Unstable Version](https://poser.pugx.org/pmvc/pmvc/v/unstable)](https://packagist.org/packages/pmvc/pmvc) 
[![Build Status](https://travis-ci.org/pmvc/pmvc.svg?branch=master)](https://travis-ci.org/pmvc/pmvc)
[![StyleCI](https://styleci.io/repos/34601083/shield)](https://styleci.io/repos/34601083)
[![License](https://poser.pugx.org/pmvc/pmvc/license)](https://packagist.org/packages/pmvc/pmvc)
[![Total Downloads](https://poser.pugx.org/pmvc/pmvc/downloads)](https://packagist.org/packages/pmvc/pmvc) 

## Getting Started
https://github.com/pmvc/pmvc/wiki

## How to use?
```
plug( 'plugin name', ['option'] );
```
#### Plugin hello world
   * Source Code
      * https://github.com/pmvc-plugin/hello_world/blob/master/hello_world.php
   * Used
      * \PMVC\plug('hello_world', ['Hello, PMVC.'])->say();

#### PlugIn features
   * setPlugInFolders
   * addPlugInFolders
   * callPlugin
   * unPlug
   * rePlug
   * getPlugs
   * initPlugIn
   * plug

## MVC
   * Controller
      * https://github.com/pmvc-plugin/controller
   * View
      * https://github.com/pmvc-plugin/?utf8=%E2%9C%93&query=view
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

## HHVM Support
[![HHVM Status](http://hhvm.h4cc.de/badge/pmvc/pmvc.svg)](http://hhvm.h4cc.de/package/pmvc/pmvc)

## PHP Version
![tested](http://php-eye.com/badge/pmvc/pmvc/tested.svg)
