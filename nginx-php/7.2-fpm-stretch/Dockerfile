FROM php:7.2-fpm-stretch

# Install package gnupg
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        gnupg

# Copy the nginx repositories to the apt source
COPY apt/nginx.list /etc/apt/sources.list.d/
# Add the signing key for the nginx repositories
RUN curl -sL https://nginx.org/keys/nginx_signing.key|apt-key add -
# Install nginx
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        nginx

# Change user of nginx
RUN sed -i -E 's/user(\s+)nginx;/user\1www-data;/' /etc/nginx/nginx.conf
# Disable the default vhost
RUN unlink /etc/nginx/conf.d/default.conf
# Copy the nginx configuration files
COPY nginx/* /etc/nginx/conf.d/

RUN apt-get update && \
    apt-get install -y --no-install-recommends supervisor

# Enable max_requests for PHP fpm
RUN sed -i 's/;pm.max_requests = 500/pm.max_requests = 500/' /usr/local/etc/php-fpm.d/www.conf
# Set listen correctly for local unix socket
RUN sed -i 's/listen = 9000/listen = \/var\/run\/php-fpm.sock/' /usr/local/etc/php-fpm.d/zz-docker.conf
RUN { \
    echo 'listen.owner = www-data'; \
    echo 'listen.group = www-data'; \
} | tee >> /usr/local/etc/php-fpm.d/zz-docker.conf
# Do not expose php
RUN echo 'expose_php = off' > /usr/local/etc/php/conf.d/security.ini
# Copy supervisord configurations
COPY supervisord/nginx.conf /etc/supervisor/conf.d/
COPY supervisord/php-fpm.conf /etc/supervisor/conf.d/

EXPOSE 80

CMD ["supervisord",  "-n", "-c", "/etc/supervisor/supervisord.conf"]
