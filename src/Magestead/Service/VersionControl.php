<?php namespace Magestead\Service;

use Magestead\Command\ProcessCommand;
use Symfony\Component\Console\Output\OutputInterface;

class VersionControl
{
    protected $_repoUrl;
    protected $_projectPath;
    protected $_output;

    /**
     * VersionControl constructor.
     * @param $repoUrl
     * @param $projectPath
     * @param OutputInterface $output
     */
    public function __construct($repoUrl, $projectPath, OutputInterface $output)
    {
        $this->_repoUrl = $repoUrl;
        $this->_projectPath = $projectPath;
        $this->_output = $output;

        $this->execute($output);
    }

    /**
     * @param OutputInterface $output
     */
    protected function execute(OutputInterface $output)
    {
        $output->writeln('<info>Configuring GIT repo</info>');
        $this->init();

        $output->writeln('<comment>Adding files to repo</comment>');
        $this->addFiles();

        $output->writeln('<comment>Committing files to repo</comment>');
        $this->commitFiles();

        $output->writeln('<comment>Pushing to remote</comment>');
        $this->pushFiles();
    }

    /**
     * Initialise the GIT repo
     *
     * @return $this
     */
    public function init()
    {
        $command = 'git init; git remote add origin ' . $this->_repoUrl;
        new ProcessCommand($command, $this->_projectPath, $this->_output);

        return $this;
    }

    /**
     * Add all file to the GIT index
     *
     * @return $this
     */
    public function addFiles()
    {
        $command = 'git add -A';
        new ProcessCommand($command, $this->_projectPath, $this->_output);

        return $this;
    }

    /**
     * Commit the files to the repo
     *
     * @return $this
     */
    public function commitFiles()
    {
        $command = "git commit -m 'Initial commit'";
        new ProcessCommand($command, $this->_projectPath, $this->_output);

        return $this;
    }

    /**
     * Push all the files to remote repo
     *
     * @return $this
     */
    public function pushFiles()
    {
        $command = "git push -u origin master";
        new ProcessCommand($command, $this->_projectPath, $this->_output);

        return $this;
    }
}
