<?php namespace Magestead\Command\VM;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StatusCommand
 * @package Magestead\Command\VM
 */
class StatusCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName("vm:status");
        $this->setDescription("Get the status of your development machine");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Getting machine status</info>');
        return new ProcessCommand('vagrant status', $this->_projectPath, $output);
    }
}
