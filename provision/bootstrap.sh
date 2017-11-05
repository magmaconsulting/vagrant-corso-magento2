#!/bin/bash

echo "Inizio configurazione iniziale..."



echo "Aggiornamento pacchetti..."
sudo apt-get update > /dev/null 2>&1

# Nginx
echo "Installazione Nginx..."
sudo apt-get install -y nginx > /dev/null 2>&1

# MySQL
echo "Preparazione per l'installazione di MySQL..."
sudo apt-get install -y debconf-utils > /dev/null 2>&1
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password root"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password root"

echo "Installazione MySQL..."
sudo apt-get install -y mysql-server > /dev/null 2>&1

echo "Installazione moduli PHP..."
sudo apt-get install -y php7.0-fpm php-mysql php-zip php-dom php-gd  php-mcrypt php-curl php-intl php-xsl php-mbstring php-json  php-soap > /dev/null 2>&1

echo "Installo altri pacchetti..."
sudo apt-get install -y wget composer > /dev/null 2>&1

echo "Preparo web tools"
cd /vagrant/default
wget http://www.adminer.org/latest.php -O adminer.php > /dev/null 2>&1
echo "<?php phpinfo(); " > info.php

echo "Aggiunto istruzioni in bash profile"
echo "cd /vagrant" >> /home/vagrant/.profile
echo 'PATH="/vagrant/bin:$PATH"' >> /home/vagrant/.profile

# Nginx Config
echo "Sovraascrivo vhost predefinito di nginx..."
sudo mv /etc/nginx/sites-available/default /etc/nginx/sites-available/default.orig
#sudo rm -rf /etc/nginx/sites-available/default > /dev/null 2>&1
cp /vagrant/provision/nginx_vhost_default /etc/nginx/sites-available/default > /dev/null 2>&1

echo "Modifico utente di nginx e PHP"
sed -i 's/user www-data/user vagrant/g' /etc/nginx/nginx.conf
sed -i 's/user = www-data/user = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf

echo "Modifico configurazione di avvio di Nginx..."
sed -i 's/WantedBy=multi-user.target/WantedBy=vagrant.mount/g' /lib/systemd/system/nginx.service

# Restarting Nginx for config to take effect
echo "Riavvio Nginx e PHP..."
sudo systemctl restart nginx > /dev/null 2>&1
sudo systemctl restart php7.0-fpm > /dev/null 2>&1
#echo "Imposto password utente "
#echo "ubuntu:vagrant" | chpasswd


echo "+---------------------------------------------------------+"
echo "|                     S U C C E S S O                     |"
echo "+---------------------------------------------------------+"
echo "| Puoi verificare il funzionamento del server puntanto    |"
echo "| il browser all'indirizzo                                |"
echo "| http://vu16lemp o all'indirizzo http://192.168.100.16   |"
echo "|                                                         |"
echo "+---------------------------------------------------------+"
