#!/bin/bash

case "$1" in
  bash)
    docker-compose run --rm phpunit bash
    ;;

  *)
    docker-compose run --rm phpunit ./phpunit-legacy.sh $* 
    ;;
esac

exit $?
