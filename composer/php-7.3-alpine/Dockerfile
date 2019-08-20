FROM php:7.3-cli-alpine

# Install composer
RUN apk add --no-cache --virtual build_deps \
            curl \
            jq; \
    EXPECTED_SIGNATURE=$(curl -s https://composer.github.io/installer.sig); \
    COMPOSER_VERSION=$(curl -s https://api.github.com/repos/composer/composer/releases | jq -r '.[] | select(has("tag_name")) | select(.tag_name | test("^1.\\d\\.\\d$")) | .tag_name '|head -n1); \
    echo ${COMPOSER_VERSION}; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    ACTUAL_SIGNATURE=$(php -r "echo hash_file('SHA384', 'composer-setup.php');"); \
    if [[ "$EXPECTED_SIGNATURE" != "$ACTUAL_SIGNATURE" ]]; then \
        >&2 echo 'ERROR: Invalid installer signature'; \
        rm composer-setup.php; \
        exit 1; \
    fi; \
    \
    php composer-setup.php --quiet --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}; \
    RESULT=$?; \
    rm composer-setup.php; \
    apk del build_deps; \
    exit ${RESULT}

# Install git
RUN apk add --no-cache \
        git \
        openssh-client \
        unzip

# Create build directory
RUN mkdir /build

COPY scripts/docker-entrypoint.sh /usr/local/bin/docker-composer-entrypoint
RUN chmod +x /usr/local/bin/docker-composer-entrypoint

WORKDIR /build

ENTRYPOINT ["docker-composer-entrypoint"]
