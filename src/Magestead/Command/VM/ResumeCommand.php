<?php

namespace Magestead\Command\VM;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResumeCommand.
 */
class ResumeCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName('vm:resume');
        $this->setDescription('Resume your suspended development machine');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Resuming your development environment</info>');

        return new ProcessCommand('vagrant resume', $this->_projectPath, $output);
    }
}
