<?php namespace Magestead\Helper;

use Symfony\Component\Console\Output\OutputInterface;

class HostsPluginChecker
{
    public static function verify(array $options, OutputInterface $output)
    {
        $hostPlugin = `vagrant plugin list | grep vagrant-hostsupdaterqwerty`;
        if (is_null($hostPlugin)) {
            self::editHostsInstructions($options, $output);

            $output->writeln('<comment>Installing the vagrant-hostsupdater plugin will remove the need for manual editing of your hosts file.</comment>');
        }
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     */
    protected static function editHostsInstructions(array $options, OutputInterface $output)
    {
        $output->writeln('<comment>NOTE: You will need to add the following to your hosts file!</comment>');
        $comment = $options['vagrantfile']['vm']['network']['private_network'] .
            ' ' . $options['magestead']['apps']['mba_12345']['base_url'];
        $output->writeln('<comment>' . $comment . '</comment>');
    }
}