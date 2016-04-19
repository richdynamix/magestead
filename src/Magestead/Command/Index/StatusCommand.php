<?php namespace Magestead\Command\Index;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StatusCommand
 * @package Magestead\Command\Index
 */
class StatusCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("index:status");
        $this->setDescription("Show status of index");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Getting index status</info>');

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
                return "cd /var/www/public;bin/magento indexer:status";
                break;
        }

        return false;
    }
}