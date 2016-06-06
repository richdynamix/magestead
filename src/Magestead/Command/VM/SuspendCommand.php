<?php

namespace Magestead\Command\VM;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SuspendCommand.
 */
class SuspendCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName('vm:suspend');
        $this->setDescription('Suspend your development machine');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Suspending your development machine</info>');

        return new ProcessCommand('vagrant suspend', $this->_projectPath, $output);
    }
}
