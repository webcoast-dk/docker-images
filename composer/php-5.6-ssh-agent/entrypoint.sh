#!/usr/bin/env bash

eval $(ssh-agent) > /dev/null
ssh-add > /dev/null

/usr/local/bin/composer "$@"
