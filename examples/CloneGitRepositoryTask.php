<?php

namespace App;

use App\Pipes\CloneGitRepository;
use App\Pipes\SetupGitDirectory;
use Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException;
use Zurbaev\PipelineTasks\Task;

class CloneGitRepositoryTask extends Task
{
    /**
     * @var string
     */
    protected $gitUrl;

    /**
     * @var string
     */
    protected $branch;

    /**
     * @var string
     */
    protected $rootPath;

    /**
     * CloneGitRepositoryTask constructor.
     *
     * @param string $gitUrl
     * @param string $branch
     * @param string $rootPath
     */
    public function __construct(string $gitUrl, string $branch, string $rootPath)
    {
        $this->gitUrl = $gitUrl;
        $this->branch = $branch;
        $this->rootPath = $rootPath;
    }

    public function pipes()
    {
        // If you have any dependencies defined in the
        // constructor and/or `handle` method, they
        // will be injected by the Laravel's DI.

        return [
            new SetupGitDirectory($this->getRootPath()),
            CloneGitRepository::class,
        ];
    }

    public function completed()
    {
        // Send successful notification here, etc.
    }

    public function failed(PipelineTaskFailedException $e)
    {
        // Send failed notification here, etc.
    }

    public function getGitUrl()
    {
        return $this->gitUrl;
    }

    public function getBranch()
    {
        return $this->branch;
    }

    public function getRootPath()
    {
        return $this->rootPath;
    }
}
