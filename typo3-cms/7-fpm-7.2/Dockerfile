FROM php:7.2-fpm-stretch

RUN apt-get update && \
    apt-get install -y ${PHPIZE_DEPS} && \
    apt-get install -y libpng16-16 libjpeg62-turbo libfreetype6 graphicsmagick ghostscript libxml2 && \
    apt-get install -y libpng-dev libjpeg62-turbo-dev libfreetype6-dev libxml2-dev && \
    docker-php-ext-configure gd --with-jpeg-dir --with-png-dir --with-freetype-dir && \
    docker-php-ext-install -j $(($(nproc) + 1)) gd mysqli opcache soap zip && \
    apt-get purge -y ${PHPIZE_DEPS} libpng-dev libjpeg62-turbo-dev libfreetype6-dev libxml2-dev && \
    apt-get autoremove -y

# Enable max_requests for PHP fpm
RUN sed -i 's/;pm.max_requests = 500/pm.max_requests = 500/' /usr/local/etc/php-fpm.d/www.conf
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
    locale-gen

ENV LC_ALL='en_US.utf8';

# Install ssmtp for mail sending mail to the defined mail relay
RUN apt-get install -y ssmtp
RUN { \
    echo 'root='; \
    echo 'mailhub=smtprelay'; \
    echo 'FromLineOverride=YES'; \
} | tee /etc/ssmtp/ssmtp.conf
