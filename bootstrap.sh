#!/bin/bash

readonly DEV_PROJECT_NAME=qmcree_com

cd /var/www/
mkdir ${DEV_PROJECT_NAME}
mkdir ${DEV_PROJECT_NAME}/public
chown -R vagrant:www-data ${DEV_PROJECT_NAME}/

# Configure nginx
cd /etc/nginx/sites-available/
touch ${DEV_PROJECT_NAME}
service php5-fpm restart
service nginx restart