<?php

namespace Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatFails;

use Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException;
use Zurbaev\PipelineTasks\Task;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatFails\Pipes\FirstPipe;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatFails\Pipes\SecondPipe;

class ExampleTaskThatFails extends Task
{
    public function pipes()
    {
        return [
            FirstPipe::class,
            SecondPipe::class,
        ];
    }

    public function failed(PipelineTaskFailedException $exception)
    {
        return 'ExceptionThrownFrom'.$exception->getPipeName();
    }
}
