<?php namespace Magestead\Command\Index;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SetModeCommand
 * @package Magestead\Command\Index
 */
class SetModeCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("index:mode:set");
        $this->setDescription("Set index mode type");
        $this->addArgument('mode', InputArgument::REQUIRED, '{realtime|schedule} [indexer]');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $indexMode = $input->getArgument('mode');
        $command   = $this->getCommand(new Config($output), $indexMode);
        if ($command) {
            $output->writeln('<info>Setting index mode</info>');
            $pCommand = "vagrant ssh -c '". $command ."'";
            return new ProcessCommand($pCommand, $this->_projectPath, $output);
        }

        $output->writeln('<error>Command not available for this application</error>');
    }

    /**
     * @param Config $config
     * @param $mode
     * @return bool|string
     */
    protected function getCommand(Config $config, $mode)
    {
        $type = $config->type;
        switch ($type) {
            case 'magento2':
                return "cd /var/www/public;bin/magento indexer:set-mode $mode";
        }

        return false;
    }
}