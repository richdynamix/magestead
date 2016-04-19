<?php namespace Magestead\Command\Index;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ReindexCommand
 * @package Magestead\Command\Index
 */
class ReindexCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("index:reindex");
        $this->setDescription("Reindex data");
        $this->addArgument('index', InputArgument::OPTIONAL, '[indexer]');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Reindexing data</info>');
        $index = $input->getArgument('index');

        $command = $this->getCommand(new Config($output), $index);
        $passedCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($passedCommand, $this->_projectPath, $output);
    }

    /**
     * @param Config $config
     * @param $index
     * @return bool|string
     */
    protected function getCommand(Config $config, $index)
    {
        $type = $config->type;
        switch ($type) {
            case 'magento':
                $index = (!is_null($index)) ? ' '.$index : ':all';
                return "cd /var/www/public;../bin/n98-magerun.phar index:reindex$index";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/magento indexer:reindex $index";
                break;
        }

        return false;
    }
}