<?php namespace Magestead\Helper;

use Magestead\Exceptions\MissingComposerHomeException;
use Magestead\Exceptions\MissingConfigFileException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class Config
{
    protected $_config;

    protected $_projectPath;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Config constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->_projectPath = getcwd();
        $this->output = $output;
    }

    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        $this->_config = $this->getConfigFile($this->output);
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

    /**
     * Find the composer home directory on non Mac environments (experimental)
     *
     * @return string
     * @throws MissingComposerHomeException
     */
    public function getComposerHomeDir()
    {
        $composerConfig = shell_exec('composer config --list --global | grep "\[home\]"');
        $composerConfig = array_filter(explode(PHP_EOL, $composerConfig));

        foreach ($composerConfig as $line) {
            $parts = array_filter(explode(" ", $line));
            $composerConfig[$parts[0]] = $parts[1];
        }

        $composerHomePath = realpath(trim($composerConfig['[home]']));

        if (false === $composerHomePath) {
            throw new MissingComposerHomeException('Composer home directory is not found. Do you have it installed?');
        }

        return $composerHomePath;
    }
}
