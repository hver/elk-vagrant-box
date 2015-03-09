# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "boxcutter/ubuntu1404"

  # Elastic search port, it needs to be open, because default Kibana looks for elasticsearch at localhost:9200
  config.vm.network "forwarded_port", guest: 9200, host: 9200

  # Kibana
  config.vm.network "forwarded_port", guest: 5601, host: 5601

  # Apache
  config.vm.network "forwarded_port", guest: 80, host: 9201

  # Supervisor
  config.vm.network "forwarded_port", guest: 9001, host: 9001


  # Create a private network, which allows host-only access to the machine using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"

  # If true, then any SSH connections made will enable agent forwarding.
  config.ssh.forward_agent = true

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder ".", "/vagrant"

  config.vm.provider :virtualbox do |v|
      v.name = "logstash-example"
      v.memory = 1024
  end

  config.vm.provision "shell", path: "provision/provision.sh", privileged: false
end
