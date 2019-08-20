FROM php:7.2-cli-stretch

# Install composer
RUN EXPECTED_SIGNATURE=$(curl -s https://composer.github.io/installer.sig); \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');"); \
    if [ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]; then \
        >&2 echo 'ERROR: Invalid installer signature'; \
        rm composer-setup.php; \
        exit 1; \
    fi; \
    \
    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer; \
    RESULT=$?; \
    rm composer-setup.php; \
    exit ${RESULT}

# Install git
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git \
        ssh-client \
        zip \
        unzip

# Add necessary locales
RUN apt-get update && \
    apt-get install -y --no-install-recommends locales && \
    { \
        echo 'en_US.utf8 UTF-8'; \
    } | tee /etc/locale.gen && \
    locale-gen
ENV LC_ALL='en_US.utf8'

# Add php zip extension as fallback
RUN apt-get update && \
    apt-get install zlib1g-dev && \
    docker-php-ext-configure zip && \
    docker-php-ext-install zip

# Create build directory
RUN mkdir /build

COPY entrypoint.sh /
RUN chmod +x /entrypoint.sh

WORKDIR /build

ENTRYPOINT ["/entrypoint.sh"]
