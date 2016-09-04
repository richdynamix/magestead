<?php namespace Magestead\Command\Index;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ShowModeCommand
 * @package Magestead\Command\Index
 */
class ShowModeCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("index:mode:show");
        $this->setDescription("Show index mode");
        $this->addArgument('index', InputArgument::OPTIONAL, '[indexer]');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $index = $input->getArgument('index');

        $command = $this->getCommand(new Config($output), $index);
        if ($command) {
            $output->writeln('<info>Getting index mode</info>');
            $passedCommand = "vagrant ssh -c '". $command ."'";
            return new ProcessCommand($passedCommand, $this->_projectPath, $output);
        }

        $output->writeln('<error>Command not available for this application</error>');
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
            case 'magento2':
                return "cd /var/www/public;bin/magento indexer:show-mode $index";
        }

        return false;
    }
}