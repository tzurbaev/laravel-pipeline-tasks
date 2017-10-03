<?php

namespace Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes;

use Zurbaev\PipelineTasks\Pipe;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\InjectedExample;

class SecondPipe extends Pipe
{
    public function handle(InjectedExample $example)
    {
        return $example->value();
    }
}
