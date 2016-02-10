require 'spec_helper_acceptance'

describe 'redis' do
  it 'should run successfully' do
    pp = <<-EOS
    Exec {
      path => [ '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin', ]
    }

    class { '::redis':
      manage_repo => true,
    }
    EOS

    # Apply twice to ensure no errors the second time.
    apply_manifest(pp, :catch_failures => true)
    apply_manifest(pp, :catch_changes => true)
  end

  describe package('redis') do
    it { should be_installed }
  end
end
