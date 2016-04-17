<?php namespace Magestead\Command\VM;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SshCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName("vm:ssh");
        $this->setDescription("Login to your development machine with SSH");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        passthru(' vagrant ssh');
    }
}