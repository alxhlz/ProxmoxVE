FROM php:8-alpine

RUN wget https://getcomposer.org/composer-stable.phar -O /usr/local/bin/composer -q && chmod +x /usr/local/bin/composer
RUN mkdir -p /root/proxmoxve/

ENTRYPOINT ["sh"]

WORKDIR /root/proxmoxve/
