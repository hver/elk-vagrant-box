#!/bin/bash
# stop on errors
set -e
set -u

# install packages
sudo apt-get update
sudo apt-get install openjdk-7-jre-headless php5 supervisor -y

# install elasticsearch
wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.4.4.deb -O elastcsearch.deb
sudo dpkg -i elastcsearch.deb
sudo update-rc.d elasticsearch defaults 95 10
echo "http.cors.enabled: true" | sudo tee -a  /etc/elasticsearch/elasticsearch.yml
[ -d /usr/share/elasticsearch/plugins/head ] || sudo /usr/share/elasticsearch/bin/plugin -install mobz/elasticsearch-head
sudo service elasticsearch restart

# install kibana
wget https://download.elasticsearch.org/kibana/kibana/kibana-4.0.1-linux-x64.tar.gz -O kibana.tar.gz
sudo mkdir -p /opt/kibana
sudo mkdir -p /var/log/kibana
sudo tar xzf kibana.tar.gz -C /opt/kibana --strip 1
sudo cp /vagrant/provision/supervisor.conf /etc/supervisor/conf.d/supervisor.conf
sudo service supervisor restart

# install logstash
wget https://download.elasticsearch.org/logstash/logstash/packages/debian/logstash_1.4.2-1-2c0f5a1_all.deb -O logstash.deb
sudo dpkg -i logstash.deb
sudo cp /vagrant/provision/logstash.conf /etc/logstash/conf.d/logstash.conf
sudo service logstash restart

# configure apache
sudo cp /vagrant/provision/apache_vhost.conf /etc/apache2/sites-available/000-default.conf
sudo service apache2 restart

# composer
cd /vagrant
curl -sS https://getcomposer.org/installer | php

# Sample log message. It helps to configure an index pattern on Kibana init
php /vagrant/examples/logstash_example.php
