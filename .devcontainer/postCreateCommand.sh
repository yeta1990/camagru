sudo chmod a+x "$(pwd)/"
sudo rm -rf /var/www/html
sudo ln -s "$(pwd)/" /var/www/html
sudo a2enmod rewrite
#echo 'error_reporting=0' | sudo tee /usr/local/etc/php/conf.d/no-warn.ini
sudo apache2ctl start