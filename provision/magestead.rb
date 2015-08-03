class Magestead
  def Magestead.configure(config, settings)
    # Set The VM Provider
    ENV['VAGRANT_DEFAULT_PROVIDER'] = settings["provider"] ||= "virtualbox"

    # Configure Local Variable To Access Scripts From Remote Location
    scriptDir = File.dirname(__FILE__)

    # Bootstrap type
    bootstrap = settings["bootstrap"] ||= nil

    # setup domain
    domain = settings["domain"] ||= "magestead.dev"

    # Path setting
    path = "/vagrant/public"
    if (bootstrap == "magento")
      path = "/vagrant/magento"
    end
    if (bootstrap == "laravel")
      path = "/vagrant/laravel/public"
    end
    if (bootstrap == "symfony")
      path = "/vagrant/symfony/web"
    end

    # Prevent TTY Errors
    config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"

    # Configure The Box
    config.vm.box = "richdynamix/magestead"
#     config.vm.box = "magestead"
    config.vm.hostname = settings["hostname"] ||= "magestead"

    # Configure A Private Network IP
    config.vm.network :private_network, ip: settings["ip"] ||= "192.168.47.10"

    config.vm.synced_folder ".", "/vagrant", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

    # Configure A Few VirtualBox Settings
    config.vm.provider "virtualbox" do |vb|
      vb.name = settings["name"] ||= "magestead"
      vb.customize ["modifyvm", :id, "--memory", settings["memory"] ||= "2048"]
      vb.customize ["modifyvm", :id, "--cpus", settings["cpus"] ||= "1"]
      vb.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
      vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
      #vb.customize ["modifyvm", :id, "--ostype", "Ubuntu_64"]
    end

    # Standardize Ports Naming Schema
    if (settings.has_key?("ports"))
      settings["ports"].each do |port|
        port["guest"] ||= port["to"]
        port["host"] ||= port["send"]
        port["protocol"] ||= "tcp"
      end
    else
      settings["ports"] = []
    end

    # Default Port Forwarding
    default_ports = {
      80   => 8000,
      443  => 44300,
      3306 => 33060,
      5432 => 54320
    }

    # Use Default Port Forwarding Unless Overridden
    default_ports.each do |guest, host|
      unless settings["ports"].any? { |mapping| mapping["guest"] == guest }
        config.vm.network "forwarded_port", guest: guest, host: host
      end
    end

    # Add Custom Ports From Configuration
    if settings.has_key?("ports")
      settings["ports"].each do |port|
        config.vm.network "forwarded_port", guest: port["guest"], host: port["host"], protocol: port["protocol"]
      end
    end

    # Configure The Public Key For SSH Access
    if settings.include? 'authorize'
      config.vm.provision "shell" do |s|
        s.inline = "echo $1 | grep -xq \"$1\" /home/vagrant/.ssh/authorized_keys || echo $1 | tee -a /home/vagrant/.ssh/authorized_keys"
        s.args = [File.read(File.expand_path(settings["authorize"]))]
      end
    end

    # Copy The SSH Private Keys To The Box
    if settings.include? 'keys'
      settings["keys"].each do |key|
        config.vm.provision "shell" do |s|
          s.privileged = false
          s.inline = "echo \"$1\" > /home/vagrant/.ssh/$2 && chmod 600 /home/vagrant/.ssh/$2"
          s.args = [File.read(File.expand_path(key)), key.split('/').last]
        end
      end
    end

    # Configure All Of The Configured Databases
    if settings.has_key?("databases")
        settings["databases"].each do |db|
          config.vm.provision "shell" do |s|
            s.path = scriptDir + "/create-mysql.sh"
            s.args = [db]
          end
        end
    end

    # Configure Blackfire.io
    if settings.has_key?("blackfire")
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/blackfire.sh"
        s.args = [
          settings["blackfire"][0]["id"],
          settings["blackfire"][0]["token"],
          settings["blackfire"][0]["client-id"],
          settings["blackfire"][0]["client-token"]
        ]
      end
    end

    # Update composer on each provision
    config.vm.provision "shell" do |s|
      s.path = scriptDir + "/composer-update.sh"
    end

    # Bootstrap Magento Intallation
    if (bootstrap == "magento")
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/magento-bootstrap.sh"
        s.args = [settings["databases"][0], domain]
      end

      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/server.sh"
        s.args = [domain, path]
      end

    end

    # Bootstrap Magento2 Intallation
    if (bootstrap == "magento2")
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/magento2-bootstrap.sh"
        s.args = [settings["databases"][0], domain]
      end
    end

    # Bootstrap Laravel Intallation
    if (bootstrap == "laravel")
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/laravel-bootstrap.sh"
      end

      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/server.sh"
        s.args = [domain, path]
      end

    end

    # Bootstrap Symfony Intallation
    if (bootstrap == "symfony")
      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/symfony-bootstrap.sh"
      end

      config.vm.provision "shell" do |s|
        s.path = scriptDir + "/server.sh"
        s.args = [domain, path]
      end
    end

  end
end
