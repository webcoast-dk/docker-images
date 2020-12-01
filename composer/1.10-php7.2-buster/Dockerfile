FROM php:7.2-cli-buster



# Create build directory
RUN mkdir /build

COPY scripts/docker-composer-entrypoint.sh /usr/local/bin/docker-composer-entrypoint
RUN chmod +x /usr/local/bin/docker-composer-entrypoint

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
    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer --version=1.10.17; \
    RESULT=$?; \
    rm composer-setup.php; \
    exit ${RESULT}

WORKDIR /build

ENTRYPOINT ["docker-composer-entrypoint"]
