#!/bin/bash

VERSION=${VERSION:-5.6}
PHPUNIT=phpunit${VERSION}

case "$1" in
  bash)
    docker-compose run --rm ${PHPUNIT} bash
    ;;

  *)
    docker-compose run --rm ${PHPUNIT} ./phpunit-legacy.sh $* 
    ;;
esac

exit $?
