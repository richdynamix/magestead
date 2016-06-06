<?php

namespace Magestead\Installers;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\HostsPluginChecker;
use Magestead\Service\Notification;
use Magestead\Service\VersionControl;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Class MagentoProject.
 */
class MagentoProject
{
    /**
     * MagentoProject constructor.
     *
     * @param array $options
     * @param array $config
     * @param $projectPath
     * @param OutputInterface $output
     */
    public function __construct(array $options, array $config, $projectPath, OutputInterface $output)
    {
        $output->writeln('<info>Installing Magento with Composer</info>');
        $this->composerInstall($projectPath, $output);

        $output->writeln('<info>Installing Magento Software</info>');
        $this->installMagento($config, $projectPath, $output);

        $this->configureTestSuites($options, $projectPath, $output);

        $output->writeln('<info>Finalising Setup</info>');
        $this->finaliseSetup($options, $projectPath, $output);
        $this->showCredentials($config, $output);

        Notification::send('Magento was successfully installed!');
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param $output
     */
    protected function installMagento(array $options, $projectPath, OutputInterface $output)
    {
        $locale = $options['magestead']['apps']['mba_12345']['locale'];
        $db_name = $options['magestead']['apps']['mba_12345']['db_name'];
        $base_url = $options['magestead']['apps']['mba_12345']['base_url'];
        $default_currency = $options['magestead']['apps']['mba_12345']['default_currency'];

        $install = 'vagrant ssh -c \'cd /var/www/public; php -f install.php -- \
--license_agreement_accepted "yes" \
--locale "'.$locale.'" \
--timezone "Europe/London" \
--default_currency "'.$default_currency.'" \
--db_host "localhost" \
--db_name "'.$db_name.'" \
--db_user "magestead" \
--db_pass "vagrant" \
--session_save "db" \
--url "http://'.$base_url.'/" \
--use_rewrites "yes" \
--skip_url_validation "yes" \
--use_secure "no" \
--use_secure_admin "no" \
--secure_base_url "http://'.$base_url.'/" \
--admin_firstname "RichDynamix" \
--admin_lastname "Magestead" \
--admin_email "admin@admin.com" \
--admin_username "admin" \
--admin_password "password123"\' ';

        new ProcessCommand($install, $projectPath, $output);

        $this->configureRedis($projectPath);
        $this->setPermissions($projectPath, $output);
        $this->installMagerun($projectPath, $output);
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function setPermissions($projectPath, OutputInterface $output)
    {
        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find var/ -type f -exec chmod 600 {} \;\'';
        $output->writeln('<comment>Setting "var" Files Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find media/ -type f -exec chmod 600 {} \;\'';
        $output->writeln('<comment>Setting "media" Files Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find var/ -type d -exec chmod 700 {} \;\'';
        $output->writeln('<comment>Setting "var" Folder Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find media/ -type d -exec chmod 700 {} \;\'';
        $output->writeln('<comment>Setting "media" Folder Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo chmod 700 includes;\'';
        $output->writeln('<comment>Setting "includes" Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo chmod 600 includes/config.php;\'';
        $output->writeln('<comment>Setting "includes/config.php" Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function installMagerun($projectPath, OutputInterface $output)
    {
        $command = 'vagrant ssh -c \'cd /var/www/bin; sudo wget https://files.magerun.net/n98-magerun.phar;\'';
        $output->writeln('<info>Downloading Magerun</info>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/bin; sudo chmod +x ./n98-magerun.phar;\'';
        $output->writeln('<comment>Setting Magerun Permissions</comment>');
        new ProcessCommand($command, $projectPath, $output);
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function finaliseSetup(array $options, $projectPath, OutputInterface $output)
    {
        $command = 'vagrant ssh -c \'cd /var/www/public; ../bin/n98-magerun.phar index:reindex:all;\'';
        $output->writeln('<comment>Reindexing Tables</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; ../bin/n98-magerun.phar cache:enable;\'';
        $output->writeln('<comment>Enabling All Cache</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; ../bin/n98-magerun.phar cache:flush;\'';
        $output->writeln('<comment>Flushing All Cache</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $this->processVcs($options, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; ../bin/n98-magerun.phar sys:check;\'';
        $output->writeln('<comment>System Check</comment>');
        new ProcessCommand($command, $projectPath, $output);
    }

    /**
     * @param array           $options
     * @param OutputInterface $output
     */
    protected function showCredentials(array $options, OutputInterface $output)
    {
        $output->writeln('<info>SUCCESS: Magestead has finished installing Magento!</info>');
        $table = new Table($output);
        $table
            ->setHeaders(['Username', 'Password', 'Base URL'])
            ->setRows([
                ['admin', 'password123', $options['magestead']['apps']['mba_12345']['base_url']],
            ]);
        $table->render();

        HostsPluginChecker::verify($options, $output);
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     *
     * @return VersionControl|null
     */
    protected function processVcs(array $options, $projectPath, OutputInterface $output)
    {
        if (!empty($options['repo_url'])) {
            copy($projectPath.'/puphpet/magestead/magento/stubs/gitignore.tmp', $projectPath.'/.gitignore');

            return new VersionControl($options['repo_url'], $projectPath, $output);
        }
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function composerInstall($projectPath, OutputInterface $output)
    {
        copy($projectPath.'/puphpet/magestead/magento/stubs/composer.tmp', $projectPath.'/composer.json');
        new ProcessCommand('composer install', $projectPath, $output);
    }

    /**
     * @param $projectPath
     */
    protected function configureRedis($projectPath)
    {
        $this->updateConfigXml($projectPath);
        $this->activateModule($projectPath);
    }

    /**
     * @param $projectPath
     */
    protected function updateConfigXml($projectPath)
    {
        $localFile = '/public/app/etc/local.xml';
        $localXml = file_get_contents($projectPath.$localFile);

        $config = new \SimpleXMLElement($localXml);

        $config->global[0]->redis_session[0]->host = '127.0.0.1';
        $config->global[0]->redis_session[0]->port = '6379';
        $config->global[0]->redis_session[0]->password = '';
        $config->global[0]->redis_session[0]->timeout = '2.5';
        $config->global[0]->redis_session[0]->persistent = '';
        $config->global[0]->redis_session[0]->db = '';
        $config->global[0]->redis_session[0]->compression_threshold = '2048';
        $config->global[0]->redis_session[0]->compression_lib = 'gzip';
        $config->global[0]->redis_session[0]->log_level = '1';
        $config->global[0]->redis_session[0]->max_concurrency = '6';
        $config->global[0]->redis_session[0]->break_after_frontend = '5';
        $config->global[0]->redis_session[0]->break_after_adminhtml = '30';
        $config->global[0]->redis_session[0]->first_lifetime = '600';
        $config->global[0]->redis_session[0]->bot_first_lifetime = '60';
        $config->global[0]->redis_session[0]->bot_lifetime = '7200';
        $config->global[0]->redis_session[0]->disable_locking = '0';
        $config->global[0]->redis_session[0]->min_lifetime = '60';
        $config->global[0]->redis_session[0]->max_lifetime = '2592000';

        file_put_contents($projectPath.$localFile, $config->asXML());
    }

    /**
     * @param $projectPath
     */
    protected function activateModule($projectPath)
    {
        $moduleFile = '/public/app/etc/modules/Cm_RedisSession.xml';
        $moduleXml = file_get_contents($projectPath.$moduleFile);
        $config = new \SimpleXMLElement($moduleXml);

        $config->modules[0]->Cm_RedisSession[0]->active = 'true';
        file_put_contents($projectPath.$moduleFile, $config->asXML());
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     *
     * @return ProcessCommand
     */
    protected function configureTestSuites(array $options, $projectPath, OutputInterface $output)
    {
        $output->writeln('<info>Configuring PHPSpec & Behat Suites</info>');
        $progress = new ProgressBar($output, 2);

        $progress->start();
        copy($projectPath.'/puphpet/magestead/magento/stubs/phpspec.yml', $projectPath.'/phpspec.yml');
        $progress->advance();
        $progress->advance();
//        $behat = $this->getBehatConfig($options, $projectPath, $output);
//        $this->saveBehatConfig($projectPath, $output, $behat, $progress);
        $progress->finish();
        echo "\n";
        new ProcessCommand('bin/phpspec r', $projectPath, $output);

        return new ProcessCommand('bin/behat --init', $projectPath, $output);
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     *
     * @return bool|mixed
     */
    protected function getBehatConfig(array $options, $projectPath, OutputInterface $output)
    {
        $yaml = new Parser();

        try {
            $behat = $yaml->parse(file_get_contents($projectPath.'/puphpet/magestead/magento/stubs/behat.yml'));

            $behat['default']['extensions']['MageTest\MagentoExtension\Extension']['base_url'] = $options['base_url'];

            return $behat;
        } catch (ParseException $e) {
            $output->writeln('<error>Unable to parse the YAML config</error>');
        }

        return false;
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     * @param $behat
     * @param $progress
     */
    protected function saveBehatConfig($projectPath, OutputInterface $output, $behat, $progress)
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($behat, 6);

        try {
            file_put_contents($projectPath.'/behat.yml', $yaml);
            $progress->advance();
        } catch (\Exception $e) {
            $output->writeln('<error>Unable to write to the YAML file</error>');
        }
    }
}
