#!/usr/bin/env php
<?php
    /**
     * Magestead CLI
     *
     * @author Steven Richardson <steven@richdynamix.com>
     */

    // set to run indefinitely if needed
    set_time_limit(0);

    // include the composer autoloader
    require_once __DIR__ . '/vendor/autoload.php';

    // import the Symfony Console Application
    use Symfony\Component\Console\Application;

    // import the Init Console Command
    use Magestead\Command\InitCommand;

    define('APP_PATH',  __DIR__);

    $app = new Application('Magestead CLI', '2.0');
    $app->addCommands(array(
        // Project Setup Command
        new Magestead\Command\SetupCommand(),

        // VM Related Commands
        new Magestead\Command\VM\UpCommand(),
        new Magestead\Command\VM\HaltCommand(),
        new Magestead\Command\VM\DestroyCommand(),
        new Magestead\Command\VM\SshCommand(),
        new Magestead\Command\VM\StatusCommand(),
        new Magestead\Command\VM\SuspendCommand(),
        new Magestead\Command\VM\ResumeCommand(),
        new Magestead\Command\VM\RunCommand(),

        // Cache Commands
        new Magestead\Command\Cache\CleanCommand(),
        new Magestead\Command\Cache\DisableCommand(),
        new Magestead\Command\Cache\EnableCommand(),
        new Magestead\Command\Cache\FlushCommand(),
        new Magestead\Command\Cache\StatusCommand(),

    ));
    $app->run();