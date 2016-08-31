<?php namespace Magestead\Command\Cache;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StatusCommand
 * @package Magestead\Command\Cache
 */
class StatusCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("cache:status");
        $this->setDescription("Checks cache status");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Checking cache status</info>');

        $command  = $this->getCommand(new Config($output));
        $pCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($pCommand, $this->_projectPath, $output);
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
                return "cd /var/www/public;../bin/n98-magerun.phar cache:list";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/magento cache:status";
                break;
        }

        return false;
    }
}