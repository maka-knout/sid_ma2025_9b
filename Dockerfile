# Usa la imagen oficial de PHP con Apache
FROM php:8.2-apache

# Habilita módulos necesarios de PHP
RUN docker-php-ext-install pdo pdo_mysql

# Habilita el módulo rewrite de Apache (útil para URLs amigables)
RUN a2enmod rewrite

# Copia el contenido de la carpeta pública al servidor web
COPY ./public /var/www/html/

# Establece permisos (opcional, para evitar problemas de escritura)
RUN chown -R www-data:www-data /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html
