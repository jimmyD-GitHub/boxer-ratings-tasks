FROM php:7.3
MAINTAINER Jimmy Dixon <jamesalandixon@gmail.com>

LABEL Description = "Scheduled tasks used to facilitate boxeratings.com functionality."

RUN apt-get update \
    && apt-get install --assume-yes -yqq apt-utils libzip-dev rsync ca-certificates vim curl git-core \
    zlib1g-dev wget cron \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#Golang
RUN curl -Lsf 'https://storage.googleapis.com/golang/go1.8.3.linux-amd64.tar.gz' | tar -C '/usr/local' -xvzf -
ENV PATH /usr/local/go/bin:$PATH
RUN go get github.com/mailhog/mhsendmail
RUN cp /root/go/bin/mhsendmail /usr/bin/mhsendmail

#S6
RUN curl -sL "https://github.com/just-containers/s6-overlay/releases/download/v1.16.0.0/s6-overlay-amd64.tar.gz" \
    | tar xz -C /

#Services
COPY docker/s6/services.d /etc/services.d
COPY docker/s6/init.sh /etc/cont-init.d/init.sh

WORKDIR /var/www/boxer-ratings-tasks/
COPY . /var/www/boxer-ratings-tasks/

#PHP
COPY docker/php/php.ini /usr/local/etc/php/
RUN touch /var/log/boxer-ratings-tasks.log

#Composer
RUN composer install --no-interaction

#Crontab
ADD docker/cron/crontab /etc/cron.d/ansible-pull
RUN chmod 0600 /etc/cron.d/ansible-pull
RUN mkdir /var/log/cron
RUN service cron stop

CMD ["/init"]
