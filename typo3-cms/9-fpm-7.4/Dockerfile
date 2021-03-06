FROM php:7.4-fpm-buster

RUN apt-get update && \
    apt-get install -y libpng16-16 \
        libjpeg62-turbo \
        libfreetype6 \
        graphicsmagick \
        ghostscript \
        libxml2 \
        libzip4 && \
    apt-get install -y libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxml2-dev \
        libzip-dev && \
    docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install -j $(($(nproc) + 1)) gd mysqli opcache soap zip && \
    apt-get purge -y \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libxml2-dev \
        libzip-dev && \
    apt-get clean

# Enable verbose error reporting
RUN { \
    echo 'error_reporting = E_ALL'; \
    echo 'display_errors = Off'; \
    echo 'log_errors = On'; \
} > /usr/local/etc/php/conf.d/error_reporting.ini

# Enable max_requests for PHP fpm
RUN sed -i 's/;pm.max_requests = 500/pm.max_requests = 500/' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i 's/;catch_workers_output = yes/catch_workers_output = yes/' /usr/local/etc/php-fpm.d/www.conf
RUN { \
    echo 'listen.owner = www-data'; \
    echo 'listen.group = www-data'; \
} | tee >> /usr/local/etc/php-fpm.d/zz-docker.conf
# Do not expose php
RUN echo 'expose_php = off' > /usr/local/etc/php/conf.d/security.ini

# Add TYPO3 specific php settings
RUN { \
    echo 'always_populate_raw_post_data=-1'; \
    echo 'max_execution_time=240'; \
    echo 'max_input_vars=1500'; \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
} | tee /usr/local/etc/php/conf.d/zz-typo3.ini

# Add necessary locales
RUN apt-get update && \
    apt-get install -y --no-install-recommends locales && \
    { \
        echo 'en_US.utf8 UTF-8'; \
    } | tee /etc/locale.gen && \
    locale-gen && \
    apt-get clean

ENV LC_ALL='en_US.utf8'

# Install msmtp for mail sending mail to the defined mail relay
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        msmtp && \
    apt-get clean

COPY ./etc/msmtprc.template /etc/msmtprc.template

# Make php use MSMTP instead of sendmail
RUN { \
    echo 'sendmail_path = /usr/bin/msmtp -t'; \
} | tee /usr/local/etc/php/conf.d/sendmail.ini


COPY ./scripts/docker-cms-entrypoint.sh /usr/bin/docker-cms-entrypoint
RUN chmod +x /usr/bin/docker-cms-entrypoint

ENTRYPOINT ["docker-cms-entrypoint"]

CMD ["php-fpm"]
