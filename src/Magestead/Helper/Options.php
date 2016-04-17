<?php namespace Magestead\Helper;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class Options
{
    protected $_app;
    protected $_server;
    protected $_phpVer = '56';
    protected $_box;
    protected $_m2Username;
    protected $_m2Password;
    protected $_ipAddress;
    protected $_cpus;
    protected $_locale;
    protected $_currency;
    protected $_baseUrl;
    protected $_repoUrl = '';

    /**
     * Options constructor.
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct($helper, InputInterface $input, OutputInterface $output)
    {
        $this->getVagrantSettings($helper, $input, $output);
        $this->getApplicationSettings($helper, $input, $output);
        $this->getMagento2Settings($helper, $input, $output);
        $this->getVersionControlSettings($helper, $input, $output);

        $this->_box = "centos65+$this->_server+php$this->_phpVer";
    }
    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
          'app' => $this->getApp(),
          'server' => $this->getServer(),
          'phpver' => $this->getPhpVer(),
          'box' => $this->getBox(),
          'm2user' => $this->getM2Username(),
          'm2pass' => $this->getM2Password(),
          'repo_url' => $this->getRepoUrl(),
          'ip_address' => $this->getIpAddress(),
          'cpus' => $this->getCpus(),
          'memory_limit' => $this->getMemorylimit(),
          'locale' => $this->getLocale(),
          'default_currency' => $this->getCurrency(),
          'base_url' => $this->getBaseUrl(),
        ];
    }

    /**
     * @return mixed
     */
    public function getApp()
    {
        return $this->_app;
    }

    /**
     * @return mixed
     */
    public function getServer()
    {
        return $this->_server;
    }

    /**
     * @return string
     */
    public function getPhpVer()
    {
        return $this->_phpVer;
    }

    /**
     * @return mixed
     */
    public function getBox()
    {
        return $this->_box;
    }

    /**
     * @return mixed
     */
    public function getM2Username()
    {
        return $this->_m2Username;
    }

    /**
     * @return mixed
     */
    public function getM2Password()
    {
        return $this->_m2Password;
    }

    /**
     * @return string
     */
    public function getMemorylimit()
    {
        return $this->_memorylimit;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->_ipAddress;
    }

    /**
     * @return mixed
     */
    public function getCpus()
    {
        return $this->_cpus;
    }

    /**
     * @return string
     */
    public function getRepoUrl()
    {
        return $this->_repoUrl;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * @return mixed
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function getVagrantSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Lets configure your project\'s VM</comment>');

        $ipQuestion = new Question("Configure the IP for your VM (192.168.47.47): ", '192.168.47.47');
        $this->_ipAddress = strtolower($helper->ask($input, $output, $ipQuestion));

        $cpuQuestion = new Question("How many CPU's would you like to use? (1): ", '1');
        $this->_cpus = strtolower($helper->ask($input, $output, $cpuQuestion));

        $memoryQuestion = new Question("Define the VM memory limit (2048): ", '2048');
        $this->_memorylimit = strtolower($helper->ask($input, $output, $memoryQuestion));
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function getApplicationSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Lets configure your project\'s application</comment>');
        $appQuestion = new ChoiceQuestion(
            "Which application do you want to install?",
            ['Magento', 'Magento 2'],
            0
        );
        $this->_app = strtolower($helper->ask($input, $output, $appQuestion));

        $baseUrlQuestion = new Question("Enter your application's base_url (magestead.dev): ", 'magestead.dev');
        $this->_baseUrl = strtolower($helper->ask($input, $output, $baseUrlQuestion));

        $currenyQuestion = new Question("Enter your application's default currency (GBP): ", 'GBP');
        $this->_currency = $helper->ask($input, $output, $currenyQuestion);

        $localeQuestion = new Question("Enter your application's default locale (en_GB): ", 'en_GB');
        $this->_locale = $helper->ask($input, $output, $localeQuestion);

        $serverQuestion = new ChoiceQuestion(
            "Which webserver would you like?",
            ['NGINX', 'Apache'],
            0
        );
        $this->_server = strtolower($helper->ask($input, $output, $serverQuestion));
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function getMagento2Settings($helper, InputInterface $input, OutputInterface $output)
    {
        // todo add php 7 enabled box
        // $this->usePhp7($helper, $input, $output);

        if ($this->_app === 'magento 2') {
            return $this->verifyAuth($helper, $input, $output);
        }

        return true;
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function getVersionControlSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $versionControl = new ConfirmationQuestion("Would you like to add your project to GIT? (yes/no) ", true);
        $versioning = $helper->ask($input, $output, $versionControl);
        if ($versioning) {
            $repoQuestion = new Question("Enter your full GitHub/BitBucket repo URL: ", '');
            $this->_repoUrl = strtolower($helper->ask($input, $output, $repoQuestion));
        }
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function askForAuth($helper, InputInterface $input, OutputInterface $output)
    {
        $username = new Question("Please enter your Magento username (public key): ", '');
        $this->_m2Username = $helper->ask($input, $output, $username);

        $password = new Question("Please enter your Magento password (private key): ", '');
        $this->_m2Password = $helper->ask($input, $output, $password);
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function verifyAuth($helper, InputInterface $input, OutputInterface $output)
    {
        $authFile = $_SERVER['HOME'] . "/.composer/auth.json";

        $authJson = file_get_contents($authFile);
        $authObj = (array)json_decode($authJson);

        if (isset($authObj['http-basic']) && isset($authObj['http-basic']->{'repo.magento.com'})) {
            return true;
        }

        $this->askForAuth($helper, $input, $output);

        $authObj['http-basic']['repo.magento.com']['username'] = $this->_m2Username;
        $authObj['http-basic']['repo.magento.com']['password'] = $this->_m2Password;

        $authJson = json_encode($authObj);
        return file_put_contents($authFile, $authJson);
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function usePhp7($helper, InputInterface $input, OutputInterface $output)
    {
        if ($this->_app !== 'magento' && $this->_server !== 'apache') {
            $phpVerQuestion = new ChoiceQuestion(
                "Which version of PHP should be installed?",
                ['56', '7'],
                0
            );
            $this->_phpVer = $helper->ask($input, $output, $phpVerQuestion);
        }
    }
}