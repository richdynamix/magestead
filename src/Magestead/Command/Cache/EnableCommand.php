<?php namespace Magestead\Command\Cache;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EnableCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("cache:enable");
        $this->setDescription("Enable cache types");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Enabling all cache types</info>');

        $command = $this->getCommand(new Config($output));
        $passedCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($passedCommand, $this->_projectPath, $output);
    }

    /**
     * @param Config $config
     * @return bool|string
     */
    protected function getCommand(Config $config)
    {
        $type = $config->type;
        switch ($type) {
            case 'magento':
                // todo add magerun commands
                return "";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/magento cache:enable";
                break;
        }

        return false;
    }
}