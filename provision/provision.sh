#!/bin/bash
# stop on errors
set -e
set -u

# install packages
sudo apt-get update
sudo apt-get install openjdk-7-jre-headless php5 -y

# install elasticsearch
[ -f elasticsearch-1.4.2.deb ] || wget -q https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.4.2.deb
sudo dpkg -i elasticsearch*.deb
sudo update-rc.d elasticsearch defaults 95 10
echo "http.cors.enabled: true" | sudo tee -a  /etc/elasticsearch/elasticsearch.yml
sudo service elasticsearch restart

# install kibana
[ -f kibana-3.1.2.tar.gz ] || wget -q https://download.elasticsearch.org/kibana/kibana/kibana-3.1.2.tar.gz
mkdir -p /vagrant/kibana
tar xzf kibana-*.tar.gz -C /vagrant/kibana --strip 1

# install logstash
[ -f logstash_1.4.2-1-2c0f5a1_all.deb ] || wget -q https://download.elasticsearch.org/logstash/logstash/packages/debian/logstash_1.4.2-1-2c0f5a1_all.deb
sudo dpkg -i logstash*.deb
sudo cp /vagrant/provision/logstash.conf /etc/logstash/conf.d/logstash.conf
sudo service logstash restart

# configure apache
sudo cp /vagrant/provision/apache_vhost.conf /etc/apache2/sites-available/000-default.conf
sudo service apache2 restart
