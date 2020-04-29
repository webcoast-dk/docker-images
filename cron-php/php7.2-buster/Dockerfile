FROM php:7.2-cli-buster

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        cron \
        msmtp && \
    apt-get clean

COPY ./etc/msmtprc.template /etc/msmtprc.template

# Make php use msmtp instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/bin/msmtp -t'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini

COPY ./scripts/docker-cron-entrypoint.sh /usr/bin/docker-cron-entrypoint
RUN chmod +x /usr/bin/docker-cron-entrypoint

ENTRYPOINT ["docker-cron-entrypoint"]

CMD ["cron", "-f", "-L 5"]
