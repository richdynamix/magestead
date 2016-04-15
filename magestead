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
        new Magestead\Command\SetupCommand(),
    ));
    $app->run();
