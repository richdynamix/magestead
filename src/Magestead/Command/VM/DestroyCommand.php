<?php

namespace Magestead\Command\VM;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DestroyCommand.
 */
class DestroyCommand extends Command
{
    /**
     * @var
     */
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName('vm:destroy');
        $this->setDescription('Destroy your development machine');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Destroying your development environment</info>');

        return new ProcessCommand('vagrant destroy --force', $this->_projectPath, $output);
    }
}
