<?php
namespace Magestead\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCommand
 * @package Magestead\Command
 */
class UpdateCommand extends Command
{
    private $projectPath;

    /**
     * Configure the command and description
     */
    public function configure()
    {
        $this->projectPath = getcwd();

        $this->setName("self-update");
        $this->setDescription("Check for new updates for Magestead CLI");
    }

    /**
     * Execute a global composer update for the package
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Checking for Updates</info>');
        return new ProcessCommand('composer global update richdynamix/magestead', $this->projectPath, $output);
    }
}