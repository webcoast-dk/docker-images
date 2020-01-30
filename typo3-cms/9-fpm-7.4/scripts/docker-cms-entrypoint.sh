#!/bin/bash

if [ -f /etc/msmtprc.template ]; then
    # Replace environment variables in MSMTP configuration
    (echo "cat <<EOF"; cat /etc/msmtprc.template; echo EOF) | sh > /etc/msmtprc
fi

# Exec the real command
exec docker-php-entrypoint "$@"
