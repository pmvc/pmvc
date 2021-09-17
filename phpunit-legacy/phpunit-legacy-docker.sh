#!/bin/bash

VERSION=${VERSION:-5.6}
PHPUNIT=phpunit${VERSION}
MY_PWD=$(pwd)

case "$1" in
  bash)
    MY_PWD=${MY_PWD} docker-compose run --rm ${PHPUNIT} bash
    ;;

  *)
    MY_PWD=${MY_PWD} docker-compose run --rm ${PHPUNIT} ${MY_PWD}/phpunit-legacy.sh $* 
    ;;
esac

exit $?
