#!/bin/sh

if [ -z $SSH_AUTH_SOCK ]; then
    eval $(ssh-agent) > /dev/null
    ssh-add > /dev/null
fi

# Allow git and shell command to be executed instead of composer
if [ "$1" = "git" -o "$1" = "sh" -o "$1" = "bash" ]; then
    "$@"
else
    /usr/local/bin/composer "$@"
fi
