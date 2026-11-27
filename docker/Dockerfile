#
# DEV STAGE PHP
#

FROM php:8.5-fpm AS php-dev

WORKDIR /var/www/html

# 1. Install git
RUN apt-get update && \
    apt-get install -y --no-install-recommends git && \
    rm -rf /var/lib/apt/lists/* && \
    git config --system user.name "Drosalys" && \
    git config --system user.email "drosalys@dev.fr"

# 2. Install needed packages
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip sockets pdo_mysql intl gd && \
    rm -f /usr/local/bin/install-php-extensions

# 4. Set PHP limits
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory_limit.ini
RUN echo "post_max_size=64M" > /usr/local/etc/php/conf.d/post_max_size.ini
RUN echo "upload_max_filesize=64M" > /usr/local/etc/php/conf.d/upload_max_filesize.ini
RUN echo "session.gc_maxlifetime=2592000" > /usr/local/etc/php/conf.d/session.ini
RUN echo "session.cookie_lifetime=2592000" >> /usr/local/etc/php/conf.d/session.ini

# 5. Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.config/symfony-cli/bin/symfony /usr/local/bin/symfony

ENV SYMFONY_HOME=/tmp/.symfony5

# 7. User & Group that will be used
ARG USER_ID=1000
ARG GROUP_ID=1000

## 8. Set the right user and group inside the container
COPY docker/user_entry_point.sh /user_entry_point.sh
RUN chmod +x /user_entry_point.sh
RUN /user_entry_point.sh ${USER_ID} ${GROUP_ID}

# 9. Switch to correct user
USER ${USER_ID}:${GROUP_ID}


#
# DEV STAGE NODE
#

FROM node:24 AS node-dev

WORKDIR /var/www/html

USER root

ARG USER_ID=1000
ARG GROUP_ID=1000

COPY --chmod=0755 docker/user_entry_point.sh /user_entry_point.sh
RUN /user_entry_point.sh ${USER_ID} ${GROUP_ID}

USER ${USER_ID}:${GROUP_ID}
