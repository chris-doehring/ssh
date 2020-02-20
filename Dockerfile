FROM debian:buster-slim

RUN apt-get update && apt-get install -y apt-transport-https lsb-release ca-certificates curl gnupg git unzip
RUN curl https://packages.sury.org/php/apt.gpg | apt-key add -
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list
RUN apt-get update && apt-get install -y php5.6-cli php5.6-xml php5.6-mbstring php5.6-zip

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer

RUN mkdir -p /usr/src/ssh
WORKDIR /usr/src/ssh

CMD ["php", "-a"]
