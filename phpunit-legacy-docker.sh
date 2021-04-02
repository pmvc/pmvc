#!/bin/bash

VERSION=phpunit${VERSION:-5.6}

case "$1" in
  bash)
    docker-compose run --rm ${VERSION} bash
    ;;

  *)
    docker-compose run --rm ${VERSION} ./phpunit-legacy.sh $* 
    ;;
esac

exit $?
