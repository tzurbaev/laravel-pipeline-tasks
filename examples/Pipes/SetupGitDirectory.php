<?php

namespace App\Pipes;

use Zurbaev\PipelineTasks\Pipe;

class SetupGitDirectory extends Pipe
{
    /**
     * @var string
     */
    protected $rootDirectory;

    /**
     * SetupGitDirectory constructor.
     *
     * @param string $rootDirectory
     */
    public function __construct(string $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    public function handle()
    {
        if (is_dir($this->rootDirectory)) {
            exec('rm -rf '.$this->rootDirectory);
        }

        mkdir($this->rootDirectory, 0777, true);

        if (!is_dir($this->rootDirectory)) {
            // If we failed to create directory, all
            // other steps can't be executed, so
            // just return false to stop task.

            return false;
        }

        return true;
    }
}
