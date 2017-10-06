Vagrant.configure("2") do |config|
  config.vm.box = "centos/7"
  config.vm.network :forwarded_port, guest: 80, host: 4567
  config.vm.network :forwarded_port, guest: 8000, host: 4568
  config.vm.synced_folder "longevo", "/var/www/html/longevo", type: "rsync", owner: "apache", group: "apache", mount_options: ["dmode=777,fmode=777"]
end
