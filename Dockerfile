FROM ubuntu:14.04
MAINTAINER Petr Karmashev (Rottenwood) <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get install -y curl \
    && curl -sL https://deb.nodesource.com/setup_0.12 | sudo bash - \
    && apt-get install -y php5 php5-cli php5-mysql php5-fpm \
       php5-redis redis-server nodejs nginx git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php

EXPOSE 7777 81
