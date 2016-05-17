<?php namespace Magestead\Command\Log;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ViewCommand
 * @package Magestead\Command\Redis
 */
class ViewCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    /**
     * Configure the view command
     */
    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("log:view");
        $this->setDescription("View a specific server log");
        $this->addArgument('log', InputArgument::REQUIRED, 'access or error');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = $input->getArgument('log');

        $output->writeln('<info>Viewing '. ucwords($log) . ' Log</info>');
        $command = $this->getCommand(new Config($output), $log);
        if (!$command) {
            return $output->writeln('<error>Command not available for this application</error>');
        }

        $pCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($pCommand, $this->_projectPath, $output);
    }

    /**
     * @param Config $config
     * @param $log
     * @return string
     */
    private function getCommand(Config $config, $log)
    {
        $server = $config->_config['magestead']['server'];
        $os = $config->_config['magestead']['os'];

        $location = $this->getLogLocation($server, $os);
        $command = 'cat /var/log/' . $location . '/' . $config->base_url . '-' . $log . '.log';

        return $command;
    }

    /**
     * @param $server
     * @param $os
     * @return string
     */
    private function getLogLocation($server, $os)
    {
        $location = 'nginx';
        if ($server != 'nginx') {
            $location = ($os == 'ubuntu14') ? 'apache2' : 'httpd';
        }

        return $location;
    }
}
