<?php namespace Magestead\Installers;

use Magestead\Command\ProcessCommand;
use Magestead\Helper\HostsPluginChecker;
use Magestead\Service\Notification;
use Magestead\Service\VersionControl;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class Magento2Project
{
    /**
     * Magento2Project constructor.
     * @param array $options
     * @param array $config
     * @param $projectPath
     * @param OutputInterface $output
     */
    public function __construct(array $options, array $config, $projectPath, OutputInterface $output)
    {
        $this->composerInstall($projectPath, $output);
        $this->installMagento($config, $projectPath, $output);
        $this->finaliseSetup($options, $projectPath, $output);
        $this->showCredentials($config, $output);

        Notification::send('Magento 2 was successfully installed!');
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function composerInstall($projectPath, OutputInterface $output)
    {
        $output->writeln('<info>Installing Magento 2 with Composer</info>');
        $command = 'composer create-project --repository-url=https://repo.magento.com/ magento/project-community-edition public';
        new ProcessCommand($command, $projectPath, $output);

//        $this->addPredisPackage($projectPath, $output);
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function addPredisPackage($projectPath, OutputInterface $output)
    {
        chdir($projectPath.'/public');

        echo getcwd();

        $output->writeln('<comment>Installing Predis package</comment>');
        $command = 'composer require predis/predis;';
        new ProcessCommand($command, $projectPath, $output);
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function installMagento(array $options, $projectPath, OutputInterface $output)
    {
        $this->setPermissions($projectPath, $output);

        $output->writeln('<info>Installing Magento 2 Software</info>');
        $locale = $options['magestead']['apps']['mba_12345']['locale'];
        $db_name = $options['magestead']['apps']['mba_12345']['db_name'];
        $base_url = $options['magestead']['apps']['mba_12345']['base_url'];
        $default_currency = $options['magestead']['apps']['mba_12345']['default_currency'];

        $install = 'vagrant ssh -c \'cd /var/www/public; bin/magento setup:install --base-url=http://'.$base_url.'/ \
--db-host=localhost \
--db-name='.$db_name.' \
--db-user=magestead \
--db-password=vagrant \
--admin-firstname=RichDynamix \
--admin-lastname=Magestead \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=password123 \
--language='.$locale.' \
--currency='.$default_currency.' \
--timezone=Europe/London \
--use-rewrites=1 \
--backend-frontname=admin \
--session-save=db \'';

        new ProcessCommand($install, $projectPath, $output);

        $this->configureRedis($projectPath, $output);
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function setPermissions($projectPath, OutputInterface $output)
    {
        $output->writeln('<info>Setting Permissions</info>');
        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find . -type d -exec chmod 700 {} \;\'';
        new ProcessCommand($command, $projectPath, $output);
        $output->writeln('<comment>Folder Permissions Set</comment>');

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo find . -type f -exec chmod 600 {} \;\'';
        new ProcessCommand($command, $projectPath, $output);
        $output->writeln('<comment>File Permissions Set</comment>');

        $command = 'vagrant ssh -c \'cd /var/www/public; sudo chmod +x bin/magento\'';
        new ProcessCommand($command, $projectPath, $output);
        $output->writeln('<comment>bin/magento Permissions Set</comment>');
    }

    /**
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function configureRedis($projectPath, OutputInterface $output)
    {
        $output->writeln('<comment>Configuring Redis Cache</comment>');
        $file = "$projectPath/public/app/etc/env.php";
        $env = include $file;

        $env['cache'] =
            array (
                'frontend' =>
                    array (
                        'default' =>
                            array (
                                'backend' => 'Cm_Cache_Backend_Redis',
                                'backend_options' =>
                                    array (
                                        'server' => '127.0.0.1',
                                        'port' => '6379',
                                        'persistent' => '',
                                        'database' => '0',
                                        'force_standalone' => '0',
                                        'connect_retries' => '1',
                                        'read_timeout' => '10',
                                        'automatic_cleaning_factor' => '0',
                                        'compress_data' => '1',
                                        'compress_tags' => '1',
                                        'compress_threshold' => '20480',
                                        'compression_lib' => 'gzip',
                                    )
                            ),
                        'page_cache' =>
                            array (
                                'backend' => 'Cm_Cache_Backend_Redis',
                                'backend_options' =>
                                    array (
                                        'server' => '127.0.0.1',
                                        'port' => '6379',
                                        'persistent' => '',
                                        'database' => '1',
                                        'force_standalone' => '0',
                                        'connect_retries' => '1',
                                        'read_timeout' => '10',
                                        'automatic_cleaning_factor' => '0',
                                        'compress_data' => '0',
                                        'compress_tags' => '1',
                                        'compress_threshold' => '20480',
                                        'compression_lib' => 'gzip',
                                    ),
                            ),
                    )
            );

        file_put_contents($file, "<?php \n \n return ".var_export($env,true).";");
    }

    /**
     * @param array $options
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function finaliseSetup(array $options, $projectPath, OutputInterface $output)
    {
        $command = 'vagrant ssh -c \'cd /var/www/public; bin/magento cache:flush;\'';
        $output->writeln('<comment>Flushing All Cache</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $command = 'vagrant ssh -c \'cd /var/www/public; bin/magento indexer:reindex; \'';
        $output->writeln('<comment>Reindexing Tables</comment>');
        new ProcessCommand($command, $projectPath, $output);

        $this->processVcs($options, $projectPath, $output);
    }

    /**
     * @param array $options
     * @param OutputInterface $output
     */
    protected function showCredentials(array $options, OutputInterface $output)
    {
        $output->writeln('<info>SUCCESS: Magestead has finished installing Magento 2!</info>');
        $table = new Table($output);
        $table
            ->setHeaders(['Username', 'Password', 'Base URL', 'Admin URI'])
            ->setRows([
                ['admin', 'password123', $options['magestead']['apps']['mba_12345']['base_url'], 'admin'],
            ]);
        $table->render();

        HostsPluginChecker::verify($options, $output);
    }

    protected function processVcs(array $options, $projectPath, OutputInterface $output)
    {
        if (!empty($options['repo_url'])) {
            copy($projectPath . "/puphpet/magestead/magento2/stubs/gitignore.tmp", $projectPath . "/.gitignore");
            return new VersionControl($options['repo_url'], $projectPath, $output);
        }
    }
}