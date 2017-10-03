<?php

namespace Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes;

use Zurbaev\PipelineTasks\Pipe;

class FirstPipe extends Pipe
{
    public function handle()
    {
        return 'FirstPipeResultFromTaskThatSucceeds';
    }

    public function overridenHandleMethod(array $payload, \Closure $next)
    {
        $payload[1]->setPipeResult($this->name(), 'FirstPipeResultFromTaskThatSucceedsWithOverridenMethod');

        return $next($payload);
    }
}
