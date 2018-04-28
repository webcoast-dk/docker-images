FROM php:7.1-cli

# Install composer
COPY install-composer.sh /
RUN chmod +x /install-composer.sh
RUN /install-composer.sh

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
ENV LC_ALL='en_US.utf8';

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
