#!/bin/bash

php /www/src/init.php;
mkdir -p /www/site/products/images;
chmod 777 /www/site/products/images;
php-fpm -F;