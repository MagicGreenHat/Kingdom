FROM ubuntu:14.04
MAINTAINER Petr Karmashev (Rottenwood) <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get install -y curl \
    && curl -sLS https://deb.nodesource.com/setup | bash - \
    && apt-get install -y php5 php5-cli php5-mysql mysql-server redis-server nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

EXPOSE 7777 8000 80
