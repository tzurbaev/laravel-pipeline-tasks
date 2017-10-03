<?php

namespace App\Pipes;

use App\CloneGitRepositoryTask;
use Zurbaev\PipelineTasks\Pipe;

class CloneGitRepository extends Pipe
{
    /**
     * @var CloneGitRepositoryTask
     */
    protected $task;

    public function handle()
    {
        // Since we have $this->task property set,
        // we can simply call its method to retrieve
        // data instead of passing it to constructor.

        $command = [
            'git clone -b %s --depth=1 %s %s',
            $this->task->getBranch(),
            $this->task->getGitUrl(),
            $this->task->getRootPath(),
        ];

        exec(call_user_func_array('sprintf', $command));

        return true;
    }
}
