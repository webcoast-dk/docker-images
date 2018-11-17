#!/bin/bash

# Replace environment variables in SSMTP configuration
(echo "cat <<EOF"; cat /etc/ssmtp/ssmtp.conf.template; echo EOF) | sh > /etc/ssmtp/ssmtp.conf

# Exec the real command
exec "$@"
