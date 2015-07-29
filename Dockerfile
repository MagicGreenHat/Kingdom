FROM ubuntu:14.04
MAINTAINER Petr Karmashev (Rottenwood) <smonkl@bk.ru>

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update \
    && apt-get install -y php5 php5-cli php5-mysql php5-curl mysql-server git curl \
    && curl -sS https://getcomposer.org/installer | php \
    && curl -sL https://deb.nodesource.com/setup | sudo bash - \
    && apt-get install -y nodejs build-essential \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && git config --global push.default simple \
	&& git config --global user.email "docker@kingdom" \
	&& git config --global user.name "Docker Git Manager"
