<?php
namespace Magestead\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessCommand
 * @package Magestead\Command
 */
class ProcessCommand
{
    /**
     * ProcessCommand constructor.
     * @param $command
     * @param $projectPath
     * @param OutputInterface $output
     */
    public function __construct($command, $projectPath, OutputInterface $output)
    {
        $this->run($command, $projectPath, $output);
    }

    /**
     * @param $command
     * @param $projectPath
     * @param OutputInterface $output
     */
    protected function run($command, $projectPath, OutputInterface $output)
    {
        $process = new Process($command, $projectPath, array_merge($_SERVER, $_ENV), null, null);

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }
}