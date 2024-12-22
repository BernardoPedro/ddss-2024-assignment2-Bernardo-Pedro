#
# Authors: Nuno Antunes <nmsa@dei.uc.pt>, João Antunes <jcfa@dei.uc.pt>
#

FROM php:7.2-apache

# Atualizar pacotes e instalar dependências necessárias
RUN apt-get update && apt-get install -y \
  libpq-dev \
  git \
  unzip \
  zip \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo pdo_pgsql pgsql

# Instalar o Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar o diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos do projeto para o contêiner
COPY . /var/www/html

# Instalar o PHPStan via Composer
RUN composer require --dev phpstan/phpstan:^1.10

# Comando padrão do contêiner
CMD ["apache2-foreground"]