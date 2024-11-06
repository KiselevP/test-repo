# from https://www.drupal.org/docs/system-requirements/php-requirements
FROM php:8.2.0-fpm-buster as app
# install Drupal bcmath
RUN docker-php-ext-install bcmath

# install the PHP extensions we need
RUN set -eux; \
	savedAptMark="$(apt-mark showmanual)"; \
    \
	# Workaround https://unix.stackexchange.com/questions/2544/how-to-work-around-release-file-expired-problem-on-a-local-mirror
    echo "Acquire::Check-Valid-Until \"false\";\nAcquire::Check-Date \"false\";" | cat > /etc/apt/apt.conf.d/10no--check-valid-until; \
	apt-get update; \
	apt-get install --no-install-recommends --no-install-suggests -y \
		libfreetype6-dev \
		libjpeg-dev \
		libpng-dev \
		libpq-dev \
		libzip-dev \
		git \
		unzip \
	; \
	\
	docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg=/usr \
	; \
	\
	docker-php-ext-install -j "$(nproc)" \
		gd \
		opcache \
		pdo_mysql \
		pdo_pgsql \
		zip \
	; \	
	\	
    # reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual;

WORKDIR /app

ARG OS_USER_ID=1000
ARG OS_GROUP_ID=1000
ARG OS_USER_NAME=byzya
ARG OS_GROUP_NAME=byzya

RUN addgroup -gid ${OS_GROUP_ID} ${OS_GROUP_NAME}
RUN adduser -uid ${OS_USER_ID} -ingroup ${OS_GROUP_NAME} ${OS_USER_NAME}

#COPY composer.* load.environment.php ./

COPY --chown=${OS_GROUP_ID}:${OS_GROUP_ID} ./web/  ./web
COPY --chown=${OS_GROUP_ID}:${OS_GROUP_ID} ./config/drupal ./config/drupal
COPY --chown=${OS_USER_NAME}:${OS_USER_NAME} composer.* load.environment.php getcomposer.sh ./

# get composer
RUN chmod a+x ./getcomposer.sh
RUN ./getcomposer.sh && rm -f ./getcomposer.sh
ENV COMPOSER_HOME="/home/${OS_USER_NAME}/.composer"
ENV COMPOSER_MEMORY_LIMIT=-1

# install Drupal via comnposer
RUN su - ${OS_USER_ID} set -eux; \
	composer install --prefer-dist --no-dev --no-interaction --no-progress --no-suggest; \
	composer drupal:scaffold; \
    composer clearcache;

# create directories
RUN mkdir -m 777 -p ./tmp ./private ./web/files

# set permissons 
RUN chown ${OS_USER_ID}:${OS_GROUP_ID} ./tmp ./web/files

# https://www.drupal.org/node/3060/release
ENV DRUPAL_VERSION 10.0

VOLUME ["/app/web"]
