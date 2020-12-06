#!/bin/bash

DIR="$( cd "$(dirname "$0")" ; pwd -P )"

phpunit --configuration $DIR/../phpunit-php8.xml
