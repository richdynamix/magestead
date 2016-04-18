<?php namespace Magestead\Command\Redis;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlushallCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("redis:flush-all");
        $this->setDescription("Flush redis storage");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Flushing Redis Storage</info>');

        $command = "redis-cli flushall";
        $passedCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($passedCommand, $this->_projectPath, $output);
    }
}
