<?php

namespace Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes;

use Zurbaev\PipelineTasks\Pipe;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\ExampleTaskThatSucceeds;

class ThirdPipe extends Pipe
{
    public function handle()
    {
        return $this->task instanceof ExampleTaskThatSucceeds ? 'TaskAvailable' : false;
    }
}
