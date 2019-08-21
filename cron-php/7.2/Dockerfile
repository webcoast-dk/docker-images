FROM php:7.2-cli-stretch

RUN apt-get update && \
    apt-get install -y --no-install-recommends cron ssmtp

# Make php use SSMTP instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/sbin/ssmtp -t -i'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini

COPY ./etc/ssmtp/ssmtp.conf.template /etc/ssmtp/ssmtp.conf.template

COPY ./scripts/docker-entrypoint.sh /usr/bin/docker-entrypoint.sh
RUN chmod +x /usr/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["cron", "-f", "-L 5"]
