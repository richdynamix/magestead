<?php
namespace Magestead\Command;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessCommand
{
    public function __construct($command, $projectPath, OutputInterface $output)
    {
        $this->run($command, $projectPath, $output);
    }

    protected function run($command, $projectPath, OutputInterface $output)
    {
        $process = new Process($command, $projectPath, array_merge($_SERVER, $_ENV), null, null);

        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });
    }
}