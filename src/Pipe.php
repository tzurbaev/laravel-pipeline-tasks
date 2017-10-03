<?php

namespace Zurbaev\PipelineTasks;

use Illuminate\Contracts\Container\Container;
use Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException;

abstract class Pipe
{
    /**
     * Task that is currently processing.
     *
     * @var Task
     */
    protected $task;

    /**
     * Get the pipe name.
     *
     * @return string
     */
    public function name()
    {
        return basename(
            str_replace('\\', '/', static::class)
        );
    }

    /**
     * Call pipe handler and detect any errors.
     *
     * @param array    $payload
     * @param \Closure $next
     *
     * @throws PipelineTaskFailedException
     *
     * @return mixed
     */
    public function process(array $payload, \Closure $next)
    {
        /**
         * @var Container $container
         * @var Task      $task
         */
        list($container, $task) = $payload;

        $this->task = $task;

        try {
            $result = $container->call([$this, 'handle']);
        } catch (\Exception $e) {
            throw PipelineTaskFailedException::exceptionThrown($this->name(), $e);
        }

        if ($result === false) {
            throw PipelineTaskFailedException::rejected($this->name());
        }

        return $next(
            [$container, $task->setPipeResult($this->name(), $result)]
        );
    }
}
