require 'beaker-rspec'

unless ENV['RS_PROVISION'] == 'no'
  hosts.each do |host|
    # Install Puppet
    if host.is_pe?
      install_pe
    else
      install_puppet
    end
  end
end

RSpec.configure do |c|
  # Project root
  proj_root = File.expand_path(File.join(File.dirname(__FILE__), '..'))

  # Readable test descriptions
  c.formatter = :documentation

  c.before :suite do
    # Install module and dependencies
    puppet_module_install(:source => proj_root, :module_name => 'redis')

    hosts.each do |host|
      shell("/bin/touch #{default['puppetpath']}/hiera.yaml")

      shell('puppet module install puppetlabs-stdlib',  { :acceptable_exit_codes => [0,1] })
    end
  end
end
