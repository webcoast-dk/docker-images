FROM php:5.6-cli-stretch

RUN apt-get update && \
    apt-get install -y --no-install-recommends cron ssmtp

# Make php use SSMTP instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/sbin/ssmtp -t -i'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini

COPY ./etc/ssmtp/ssmtp.conf.template /etc/ssmtp/ssmtp.conf.template

COPY ./scripts/docker-cron-entrypoint.sh /usr/bin/docker-cron-entrypoint
RUN chmod +x /usr/bin/docker-cron-entrypoint

ENTRYPOINT ["docker-cron-entrypoint"]

CMD ["cron", "-f", "-L 5"]
