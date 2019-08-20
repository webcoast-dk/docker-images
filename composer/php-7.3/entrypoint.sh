#!/usr/bin/env bash

if [ -z $SSH_AUTH_SOCK ]; then
    eval $(ssh-agent) > /dev/null
    ssh-add > /dev/null
fi

/usr/local/bin/composer "$@"
