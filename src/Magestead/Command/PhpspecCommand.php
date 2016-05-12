<?php namespace Magestead\Command;

use Magestead\Helper\Config;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PhpspecCommand
 * @package Magestead\Command
 */
class PhpspecCommand extends Command
{
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();

        $this->setName("phpspec");
        $this->setDescription("Run PHPSpec against your project");
        $this->addArgument('option', InputArgument::OPTIONAL, 'Add options to run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $option = $input->getArgument('option');

        $command = $this->getCommand(new Config($output), $option);
        if (!$command) {
            return $output->writeln('<error>Command not available for this application</error>');
        }

        $output->writeln('<info>Running PHPSpec</info>');
        $passedCommand = "vagrant ssh -c '". $command ."'";
        passthru($passedCommand);
    }

    /**
     * @param Config $config
     * @param $option
     * @return bool|string
     */
    protected function getCommand(Config $config, $option)
    {
        $type = $config->type;
        switch ($type) {
            case 'magento':
                return "cd /var/www;bin/phpspec $option";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/phpspec $option";
                break;
        }

        return false;
    }
}
