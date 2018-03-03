FROM webcoastdk/nginx-php:7.2-fpm-stretch

# Install php mysqli extension and mysql client
RUN apt-get update && \
    apt-get install -y --no-install-recommends mysql-client && \
    docker-php-ext-configure mysqli && \
    docker-php-ext-install mysqli

# Install php gd extension and graphics magick
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        graphicsmagick \
        ghostscript && \
    docker-php-ext-configure gd --with-jpeg-dir --with-png-dir --with-freetype-dir && \
    docker-php-ext-install gd

# Install php soap extension
RUN apt-get update && \
    apt-get install -y --no-install-recommends libxml2-dev && \
    docker-php-ext-configure soap && \
    docker-php-ext-install soap

# Install php zip extension
RUN docker-php-ext-configure zip && \
    docker-php-ext-install zip

# Install php opcache extension
RUN docker-php-ext-configure opcache && \
    docker-php-ext-install opcache

# Add TYPO3 specific php settings
RUN { \
    echo 'always_populate_raw_post_data=-1'; \
    echo 'max_execution_time=240'; \
    echo 'max_input_vars=1500'; \
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
RUN apt-get update && \
    apt-get install -y --no-install-recommends ssmtp
RUN { \
    echo 'root='; \
    echo 'mailhub=smtprelay'; \
    echo 'FromLineOverride=YES'; \
} | tee /etc/ssmtp/ssmtp.conf
