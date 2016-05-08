<?php namespace Magestead\Helper;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class Options
 * @package Magestead\Helper
 */
class Options
{
    const BOX_PREFIX       = 'richdynamix/magestead-';

    protected $_app        = 'magento2';
    protected $_phpVer     = '56';
    protected $_os         = 'centos65';
    protected $_server;
    protected $_box;
    protected $_m2Username;
    protected $_m2Password;
    protected $_ipAddress;
    protected $_memorylimit;
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
        $this->setVagrantSettings($helper, $input, $output);

        $this->setServerConfig($helper, $input, $output);

        $this->setApplicationSettings($helper, $input, $output);
        $this->setMagento2Settings($helper, $input, $output);
        $this->setVersionControlSettings($helper, $input, $output);

        $this->setVagrantBox();

    }
    /**
     * @return array
     */
    public function getAllOptions()
    {
        return [
          'app' => $this->_app,
          'server' => $this->_server,
          'phpver' => $this->_phpVer,
          'os' => $this->_os,
          'box' => $this->_box,
          'm2user' => $this->_m2Username,
          'm2pass' => $this->_m2Password,
          'repo_url' => $this->_repoUrl,
          'ip_address' => $this->_ipAddress,
          'cpus' => $this->_cpus,
          'memory_limit' => $this->_memorylimit,
          'locale' => $this->_locale,
          'default_currency' => $this->_currency,
          'base_url' => $this->_baseUrl,
        ];
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setVagrantSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Lets configure your project\'s VM</comment>');

        $ipQuestion       = new Question("Configure the IP for your VM (192.168.47.47): ", '192.168.47.47');
        $this->_ipAddress = strtolower($helper->ask($input, $output, $ipQuestion));

        $cpuQuestion = new Question("How many CPU's would you like to use? (1): ", '1');
        $this->_cpus = strtolower($helper->ask($input, $output, $cpuQuestion));

        $memoryQuestion     = new Question("Define the VM memory limit (2048): ", '2048');
        $this->_memorylimit = strtolower($helper->ask($input, $output, $memoryQuestion));
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setApplicationSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Lets configure your project\'s application</comment>');
        if ($this->_phpVer !== '70') {
            $appQuestion = new ChoiceQuestion(
                "Which application do you want to install?",
                ['Magento', 'Magento 2'],
                0
            );

            $this->_app = strtolower($helper->ask($input, $output, $appQuestion));
        }

        $baseUrlQuestion = new Question("Enter your application's base_url (magestead.dev): ", 'magestead.dev');
        $this->_baseUrl  = strtolower($helper->ask($input, $output, $baseUrlQuestion));

        $currenyQuestion = new Question("Enter your application's default currency (GBP): ", 'GBP');
        $this->_currency = $helper->ask($input, $output, $currenyQuestion);

        $localeQuestion = new Question("Enter your application's default locale (en_GB): ", 'en_GB');
        $this->_locale  = $helper->ask($input, $output, $localeQuestion);
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return boolean|integer
     */
    protected function setMagento2Settings($helper, InputInterface $input, OutputInterface $output)
    {
        if ($this->_app === 'magento2') {
            return $this->verifyAuth($helper, $input, $output);
        }

        return true;
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setVersionControlSettings($helper, InputInterface $input, OutputInterface $output)
    {
        $versionControl = new ConfirmationQuestion("Would you like to add your project to GIT? (no/yes) ", false);
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
        $username          = new Question("Please enter your Magento username (public key): ", '');
        $this->_m2Username = $helper->ask($input, $output, $username);

        $password          = new Question("Please enter your Magento password (private key): ", '');
        $this->_m2Password = $helper->ask($input, $output, $password);
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return boolean|integer
     */
    protected function verifyAuth($helper, InputInterface $input, OutputInterface $output)
    {
        $authFile = $_SERVER['HOME'] . "/.composer/auth.json";

        $authObj = [];
        if (file_exists($authFile)) {
            $authJson = file_get_contents($authFile);
            $authObj  = (array)json_decode($authJson);

            if (isset($authObj['http-basic']) && isset($authObj['http-basic']->{'repo.magento.com'})) {
                return true;
            }
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
    protected function setPhp($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Keep in mind PHP7 is only available for Magento 2</info>');
        $phpVerQuestion = new ChoiceQuestion(
            "Which version of PHP should be installed?",
            ['56', '70'],
            0
        );

        $this->_phpVer = $helper->ask($input, $output, $phpVerQuestion);
    }

    /**
     * Set box name from concat user options
     */
    protected function setVagrantBox()
    {
        $this->_box = self::BOX_PREFIX . $this->_os . "-$this->_server-php$this->_phpVer";
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setServerConfig($helper, InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Lets configure your server</comment>');
        $this->setOperatingSystem($helper, $input, $output);
        $this->setWebServer($helper, $input, $output);
        $this->setPhp($helper, $input, $output);
    }

    /**
     * @param $helper
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function setWebServer($helper, InputInterface $input, OutputInterface $output)
    {
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
     */
    protected function setOperatingSystem($helper, InputInterface $input, OutputInterface $output)
    {
        $osQuestion = new ChoiceQuestion(
            "Which OS would you like to install?",
            ['CentOS 6.5', 'Ubuntu 14'],
            0
        );
        $this->_os = str_replace(' ', '', str_replace('.', '', strtolower($helper->ask($input, $output, $osQuestion))));
    }
}