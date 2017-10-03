<?php

namespace Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds;

use Zurbaev\PipelineTasks\Task;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes\FirstPipe;

class ExampleTaskThatSucceeds extends Task
{
    public function pipes()
    {
        return [
            FirstPipe::class,
        ];
    }
}
