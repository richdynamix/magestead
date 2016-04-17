<?php namespace Magestead\Command\VM;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RunCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName("vm:run");
        $this->setDescription("Run commands on your development machine via SSH");
        $this->addArgument('ssh-command', InputArgument::REQUIRED, 'The command to pass to the machine.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $input->getArgument('ssh-command');
        $output->writeln('<info>Running "'.$command.'" on Magestead</info>');

        $passedCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($passedCommand, $this->_projectPath, $output);
    }
}