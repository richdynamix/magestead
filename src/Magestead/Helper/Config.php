<?php namespace Magestead\Helper;

use Magestead\Exceptions\MissingConfigFileException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class Config
{
    protected $_projectPath;

    /**
     * Config constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->_projectPath = getcwd();
        $this->_config      = $this->getConfigFile($output);
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->_config['magestead']['apps']['mba_12345'][$name];
    }

    /**
     * @param OutputInterface $output
     * @return bool|mixed
     */
    protected function getConfigFile(OutputInterface $output)
    {
        $config = new Parser();
        try {
            return $config->parse($this->readConfigFile());
        } catch (ParseException $e) {
            $output->writeln('<error>Unable to parse the config file</error>');
        }

        return false;
    }

    /**
     * @return string
     * @throws MissingConfigFileException
     */
    protected function readConfigFile()
    {
        if (!file_exists($this->_projectPath . '/magestead.yaml')) {
            throw new MissingConfigFileException('No config file was found, are you in the project root?');
        }

        return file_get_contents($this->_projectPath . '/magestead.yaml');
    }
}
