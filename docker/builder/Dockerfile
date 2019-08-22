FROM php:7.2-cli-alpine

RUN apk add --no-cache \
        curl \
        jq

COPY scripts/* /usr/local/bin/

RUN chmod +x /usr/local/bin/*
