#!/bin/bash
sudo apt-get update
sudo apt-get install openjdk-7-jre-headless nginx -y

# install elasticsearch
wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.4.2.deb
sudo dpkg -i elasticsearch*.deb
sudo update-rc.d elasticsearch defaults 95 10
echo "http.cors.enabled: true" | sudo tee -a  /etc/elasticsearch/elasticsearch.yml
sudo service elasticsearch restart

# install nginx and kibana
wget https://download.elasticsearch.org/kibana/kibana/kibana-3.1.2.tar.gz
sudo tar xzf kibana-*.tar.gz -C /usr/share/nginx/html --strip 1

# install logstash
wget https://download.elasticsearch.org/logstash/logstash/packages/debian/logstash_1.4.2-1-2c0f5a1_all.deb
sudo dpkg -i logstash*.deb
sudo cp /vagrant/logstash.conf /etc/logstash/conf.d/logstash.conf
sudo service logstash restart

echo "Hello World!" | nc localhost 12000
