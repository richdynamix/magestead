<?php namespace Magestead\Command;

use Magestead\Helper\Options;
use Magestead\Installers\Project;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;

class SetupCommand extends Command
{
    protected $_basePath;
    protected $_projectPath;
    protected $_magesteadConfig;

    protected function configure()
    {
        $this->_basePath = dirname( __FILE__ ) . '/../../../';
        $this->_projectPath = getcwd();

        $this->setName("setup");
        $this->setDescription("Initialise Magestead project into current working directory");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return \Magestead\Installers\Magento2Project|\Magestead\Installers\MagentoProject
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $options = new Options($helper, $input, $output);

        $this->setupProject($output, $options);

        $output->writeln('<info>Spinning up your custom box</info>');
        new ProcessCommand('vagrant up', $this->_projectPath, $output);

        return Project::create($options->getAllOptions(), $this->_magesteadConfig, $this->_projectPath, $output);
    }

    /**
     * @param $source
     * @param $target
     * @param OutputInterface $output
     */
    protected function copyConfigFiles($source, $target, OutputInterface $output)
    {
        try {
            $progress = new ProgressBar($output, 3720);
            $progress->start();
            foreach (
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST) as $item
            ) {
                if ($item->isDir()) {
                    mkdir($target . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                } else {
                    copy($item, $target . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                }
                $progress->advance();
            }
            $progress->finish();
            echo "\n";
        } catch (\Exception $e) {
            $output->writeln('<error>There was an error while setting up the project structure</error>');
        }
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     */
    protected function configureProject(array $options, OutputInterface $output)
    {
        $this->_magesteadConfig = $this->getConfigFile($output);

        $this->_magesteadConfig['vagrantfile']['vm']['box'] = $options['box'];
        $this->_magesteadConfig['vagrantfile']['vm']['box_url'] = $options['box'];
        $this->_magesteadConfig['vagrantfile']['vm']['memory'] = $options['memory_limit'];
        $this->_magesteadConfig['vagrantfile']['vm']['network']['private_network'] = $options['ip_address'];
        $this->_magesteadConfig['magestead']['apps']['mba_12345']['type'] = ($options['app'] == 'magento 2') ? 'magento2' : 'magento';
        $this->_magesteadConfig['magestead']['apps']['mba_12345']['locale'] = $options['locale'];
        $this->_magesteadConfig['magestead']['apps']['mba_12345']['default_currency'] = $options['default_currency'];
        $this->_magesteadConfig['magestead']['apps']['mba_12345']['base_url'] = $options['base_url'];

        $this->saveConfigFile($this->_magesteadConfig, $output);

    }

    /**
     * @param OutputInterface $output
     * @return mixed
     */
    protected function getConfigFile(OutputInterface $output)
    {
        $yaml = new Parser();
        try {
            return $yaml->parse(file_get_contents($this->_projectPath . '/magestead.yaml'));
        } catch (ParseException $e) {
            $output->writeln('<error>Unable to parse the YAML string</error>');
            printf("Unable to parse the YAML string: %s", $e->getMessage());
        }
    }

    /**
     * @param array $config
     * @param OutputInterface $output
     */
    protected function saveConfigFile(array $config, OutputInterface $output)
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($config, 6);

        try {
            file_put_contents($this->_projectPath . '/magestead.yaml', $yaml);
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to write to the YAML file</error>');
        }
    }

    /**
     * @param OutputInterface $output
     * @param $options
     */
    protected function setupProject(OutputInterface $output, $options)
    {
        $output->writeln('<info>Setting up project structure</info>');
        $provisionFolder = $this->_basePath . "provision";
        $this->copyConfigFiles($provisionFolder, $this->_projectPath, $output);
        $this->configureProject($options->getAllOptions(), $output);
    }
}