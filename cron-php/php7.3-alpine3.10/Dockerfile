FROM php:7.3-cli-alpine3.10

RUN apk add --no-cache \
        dcron \
        msmtp

COPY ./etc/msmtprc.template /etc/msmtprc.template

# Make php use SSMTP instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/bin/msmtp -t'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini

COPY ./scripts/docker-cron-entrypoint.sh /usr/bin/docker-cron-entrypoint
RUN chmod +x /usr/bin/docker-cron-entrypoint

ENTRYPOINT ["docker-cron-entrypoint"]

CMD ["crond", "-f", "-L /dev/stdout"]
