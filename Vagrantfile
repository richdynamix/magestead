require 'json'
require 'yaml'

VAGRANTFILE_API_VERSION = "2"

magesteadYamlPath = File.dirname(__FILE__) + '/provision/Magestead.yaml')
afterScriptPath = File.dirname(__FILE__) + '/provision/after.sh'
aliasesPath = File.dirname(__FILE__) + '/provision/aliases'

require File.expand_path(File.dirname(__FILE__) + '/provision/magestead.rb')

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
	if File.exists? aliasesPath then
		config.vm.provision "file", source: aliasesPath, destination: "~/.bash_aliases"
	end

	Magestead.configure(config, YAML::load(File.read(magesteadYamlPath))

	if File.exists? afterScriptPath then
		config.vm.provision "shell", path: afterScriptPath
	end
end
