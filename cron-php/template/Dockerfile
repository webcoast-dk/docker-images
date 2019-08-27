FROM php:<!-- VAR phpVersion -->-cli-<!-- VAR distribution -->

<!-- INSERT install_packages -->

<!-- INSERT mail_forwarding -->

COPY ./scripts/docker-cron-entrypoint.sh /usr/bin/docker-cron-entrypoint
RUN chmod +x /usr/bin/docker-cron-entrypoint

ENTRYPOINT ["docker-cron-entrypoint"]

<!-- INSERT command -->
