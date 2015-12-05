FROM ubuntu:14.04
MAINTAINER Petr Karmashev (Rottenwood) <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

COPY app/docker/etc/apt /etc/apt

RUN apt-get install -y --force-yes curl \
    && curl -sL https://deb.nodesource.com/setup_4.x | sudo bash -

RUN apt-get install -y --force-yes \
    redis-server \
    nodejs \
    nginx \
    git \
    php7.0 \
    php7.0-cli \
    php7.0-curl \
    php7.0-fpm \
    php7.0-mysql

RUN apt-get clean && rm -rf /var/lib/apt/lists/* && rm -rf /tmp/*

RUN curl -sS https://getcomposer.org/installer | php

EXPOSE 7777 81
