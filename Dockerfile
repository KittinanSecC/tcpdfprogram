FROM php:8.2-apache

# copy ไฟล์ทั้งหมดไปยัง server
COPY . /var/www/html/

# เปิด mod_rewrite
RUN a2enmod rewrite
