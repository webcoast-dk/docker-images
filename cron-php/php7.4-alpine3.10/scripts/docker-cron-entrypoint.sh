#!/bin/sh

if [ -f /etc/ssmtp/ssmtp.conf.template ]; then
    # Replace environment variables in SSMTP configuration
    (echo "cat <<EOF"; cat /etc/ssmtp/ssmtp.conf.template; echo EOF) | sh > /etc/ssmtp/ssmtp.conf
fi

if [ -f /etc/msmtprc.template ]; then
    # Replace environment variables in MSMTP configuration
    (echo "cat <<EOF"; cat /etc/msmtprc.template; echo EOF) | sh > /etc/msmtprc
fi

# Exec the real command (do not use `exec` to create new PID for cron command to avoid crashing of dcron in Alpine images)
"$@"
