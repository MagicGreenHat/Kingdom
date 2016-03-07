Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.network "forwarded_port", guest: 81, host: 8000
  config.vm.network "forwarded_port", guest: 7777, host: 7777
  config.vm.synced_folder ".", "/kingdom"

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false
    vb.memory = "512"
  end

  config.vm.provision "shell", inline: <<-SHELL
    curl -sSL https://get.docker.com/ | sh
    sudo usermod -aG docker vagrant
  SHELL

  config.trigger.after :up do
    run_remote "cd /kingdom && ./kingdom.sh start dev"
  end

end
