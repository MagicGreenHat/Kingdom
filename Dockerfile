FROM ubuntu:14.04
MAINTAINER Petr Karmashev (Rottenwood) <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get install -y curl \
    && curl -sLS https://deb.nodesource.com/setup | bash - \
    && apt-get install -y php5 php5-cli php5-mysql mysql-server redis-server nodejs \
    && curl -sLS https://getcomposer.org/installer | php \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*
