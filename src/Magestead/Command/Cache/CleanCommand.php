<?php namespace Magestead\Command\Cache;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanCommand
 * @package Magestead\Command\Cache
 */
class CleanCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("cache:clean");
        $this->setDescription("Cleans cache types");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Cleaning all cache types</info>');

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
                return "cd /var/www/public;../bin/n98-magerun.phar cache:clean";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/magento cache:clean";
                break;
        }

        return false;
    }
}