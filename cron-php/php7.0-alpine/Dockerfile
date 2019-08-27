FROM php:7.0-cli-alpine

RUN apk add --no-cache \
        dcron \
        ssmtp

COPY ./etc/ssmtp/ssmtp.conf.template /etc/ssmtp/ssmtp.conf.template

# Make php use SSMTP instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/sbin/ssmtp -t -i'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini

COPY ./scripts/docker-cron-entrypoint.sh /usr/bin/docker-cron-entrypoint
RUN chmod +x /usr/bin/docker-cron-entrypoint

ENTRYPOINT ["docker-cron-entrypoint"]

CMD ["crond", "-f", "-L /dev/stdout"]
