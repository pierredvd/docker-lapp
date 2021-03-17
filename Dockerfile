FROM debian:buster-slim
EXPOSE 80 443 5432

RUN 	apt-get upgrade -y
RUN 	apt-get install dpkg -y
RUN 	apt install -f
RUN 	dpkg --configure -a
RUN 	apt-get update

# Utilities
RUN 	apt-get install apt-utils -y
RUN 	apt-get install gnupg2 -y
RUN 	apt-get install sudo -y
RUN 	apt-get install curl software-properties-common -y
RUN 	apt-get install wget -y
RUN 	apt-get install vim nano -y
RUN 	apt-get install apt-transport-https -y
RUN 	apt-get install lsb-release -y
RUN 	apt-get install ca-certificates -y

# POSTGRES RIGHTS
RUN 	set -eux; \
    	groupadd -r postgres --gid=1000; \
    	useradd -r -g postgres --uid=1000 --home-dir=/var/lib/postgresql --shell=/bin/bash postgres; \
    	mkdir -p /var/lib/postgresql

# Enable PPA
COPY	/config/php/php.gpg /etc/apt/trusted.gpg.d/php.gpg
RUN 	echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php7.list
RUN 	apt-get update

# APACHE 2.24 + PHP 7.4 INSTALL
RUN 	apt-get install apache2 libapache2-mod-php7.4 -y
RUN	    apt-get install php7.4 php7.4-cli php7.4-common -y
RUN 	apt-get install	php7.4-json \
		php7.4-opcache \
		php7.4-zip \
		php7.4-fpm \
		php7.4-mbstring \
		php7.4-gd \
		php7.4-curl \
		libpq5 php7.4-pgsql -y

# APACHE 2.24 + PHP 7.4 CONFIGURATION
COPY 	./config/php/apache2.conf /etc/apache2/apache2.conf
COPY 	./config/php/ports.conf /etc/apache2/ports.conf
COPY 	./config/php/php.ini /etc/php/7.4/apache2/php.ini
RUN 	a2dissite 000-default.conf
RUN 	rm -rf /etc/apache2/sites-enabled/*
RUN 	rm -rf /etc/apache2/sites-available/*
RUN 	a2enmod ssl rewrite headers
RUN 	phpenmod pdo_pgsql curl gd

# POSTGRES INSTALL
RUN 	apt-get install libpq5 postgresql postgresql-11 postgresql-client-11 postgresql-client-common -y

# POSTGRES CONFIGURATION
COPY 	./config/postgres/pg_hba.conf /etc/postgresql/11/main/pg_hba.conf
RUN  	chown postgres /etc/postgresql/11/main/pg_hba.conf
RUN  	chgrp postgres /etc/postgresql/11/main/pg_hba.conf
RUN  	chmod 0644 /etc/postgresql/11/main/pg_hba.conf
COPY 	./config/postgres/postgresql.conf /etc/postgresql/11/main/postgresql.conf
RUN  	chown postgres /etc/postgresql/11/main/postgresql.conf
RUN  	chgrp postgres /etc/postgresql/11/main/postgresql.conf
RUN  	chmod 0644 /etc/postgresql/11/main/postgresql.conf

# POSTGRES DATA
RUN 	mkdir -p "/var/lib/postgresql/data" && \
		chown -R postgres:postgres "/var/lib/postgresql/data" && \
		chmod 777 "/var/lib/postgresql/data"
USER 	postgres
RUN 	/usr/lib/postgresql/11/bin/initdb -D /var/lib/postgresql/data -E UTF8
USER 	root

# GIT
RUN     apt-get install git -y

# NODEJS 8 INSTALL
COPY 	./config/node/node-v8.10.0-linux-x64.tar.xz /tmp/node-v8.10.0-linux-x64.tar.xz
RUN 	tar -C /usr/local --strip-components 1 -xJf /tmp/node-v8.10.0-linux-x64.tar.xz
RUN  	apt-get install npm -y

# CLEAN UP APT AND TEMPORARIES
RUN   	apt-get clean
RUN   	rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# VOLUME
RUN 	chmod -R 2775 /var/www
VOLUME 	["/var/www", "/var/lib/postgresql/data"]

# BOOTLOAD
COPY 	./config/init.sh /usr/sbin/init.sh
RUN  	chmod 0775 /usr/sbin/init.sh
CMD  	/usr/sbin/init.sh && /bin/bash