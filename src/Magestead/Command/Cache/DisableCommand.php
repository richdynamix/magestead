<?php namespace Magestead\Command\Cache;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DisableCommand
 * @package Magestead\Command\Cache
 */
class DisableCommand extends Command
{
    protected $_config;
    protected $_projectPath;

    protected function configure()
    {
        $this->_projectPath = getcwd();
        $this->setName("cache:disable");
        $this->setDescription("Disable cache types");
        $this->addArgument('type', InputArgument::OPTIONAL, '[cache code/type]');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return ProcessCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Disabling all cache types</info>');

        $cacheType = $input->getArgument('type');
        $command  = $this->getCommand(new Config($output), $cacheType);
        $pCommand = "vagrant ssh -c '". $command ."'";
        return new ProcessCommand($pCommand, $this->_projectPath, $output);
    }

    /**
     * @param Config $config
     * @return bool|string
     */
    protected function getCommand(Config $config, $cacheType)
    {
        $type = $config->type;
        switch ($type) {
            case 'magento':
                return "cd /var/www/public;../bin/n98-magerun.phar cache:disable $cacheType";
                break;
            case 'magento2':
                return "cd /var/www/public;bin/magento cache:disable $cacheType";
                break;
        }

        return false;
    }
}