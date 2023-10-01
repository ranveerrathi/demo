FROM pimcore/pimcore:PHP8.0-fpm

ENV ACCEPT_EULA=Y

ENV TZ="America/Detroit"

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update -y

RUN apt -y --no-install-recommends install curl

RUN curl -s https://packages.microsoft.com/keys/microsoft.asc | apt-key add -

RUN bash -c "curl -s https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list"

RUN apt-get clean && apt-get update -y

RUN apt -y --no-install-recommends  install gcc g++ make autoconf libc-dev pkg-config libltdl-dev

#RUN apt -y --no-install-recommends  install odbcinst=2.3.7 odbcinst1debian2=2.3.7 unixodbc-dev=2.3.7 unixodbc=2.3.7 msodbcsql17 mssql-tools libtool 

#RUN pecl install sqlsrv && pecl install pdo_sqlsrv

#RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

RUN apt-get install -y libssh2-1-dev libssh2-1 && pecl install ssh2-1.3.1 && docker-php-ext-enable ssh2

RUN echo "php_admin_value[memory_limit] = 1G" >> /usr/local/etc/php-fpm.d/www.conf

RUN echo "php_admin_value[upload_max_filesize] = 250M" >> /usr/local/etc/php-fpm.d/www.conf

RUN echo "php_admin_value[post_max_size] = 250M" >> /usr/local/etc/php-fpm.d/www.conf

RUN sed -i -E 's/(CipherString\s*=\s*DEFAULT@SECLEVEL=)2/\11/' /etc/ssl/openssl.cnf