<?php

namespace Zurbaev\PipelineTasks;

use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Pipeline\Pipeline;
use Zurbaev\PipelineTasks\Events\PipelineTaskCompleted;
use Zurbaev\PipelineTasks\Events\PipelineTaskFailed;
use Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException;

class TaskManager
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * TaskManager constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Start given task.
     *
     * @param Task $task
     *
     * @return mixed
     */
    public function start(Task $task)
    {
        try {
            return $this->pipeline()
                ->send([$this->getContainer(), $task])
                ->through($task->pipes())
                ->via($task->pipeMethod())
                ->then(function (array $payload) {
                    list(, $task) = $payload;

                    return $this->taskCompleted($task);
                });
        } catch (PipelineTaskFailedException $e) {
            return $this->taskFailed($task, $e);
        }
    }

    /**
     * Marks task as completed.
     *
     * @param Task $task
     *
     * @return mixed
     */
    protected function taskCompleted(Task $task)
    {
        $this->fire(new PipelineTaskCompleted($task));

        return $task->completed();
    }

    /**
     * Marks task as failed.
     *
     * @param Task                        $task
     * @param PipelineTaskFailedException $e
     *
     * @return mixed
     */
    protected function taskFailed(Task $task, PipelineTaskFailedException $e)
    {
        $this->fire(new PipelineTaskFailed($task, $e));

        return $task->failed($e);
    }

    /**
     * Get container instance.
     *
     * @return \Illuminate\Contracts\Container\Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Create new pipeline instance.
     *
     * @return Pipeline
     */
    protected function pipeline()
    {
        return new Pipeline($this->getContainer());
    }

    /**
     * Fire given event.
     *
     * @param mixed $event
     *
     * @return mixed
     */
    protected function fire($event)
    {
        if (!$this->hasEvents()) {
            return false;
        }

        return $this->getEvents()->dispatch($event);
    }

    /**
     * Determines if manager's container has events dispatcher bound.
     *
     * @return bool
     */
    protected function hasEvents()
    {
        return $this->getContainer()->bound('events');
    }

    /**
     * Get the events dispatcher instance.
     *
     * @return Dispatcher
     */
    protected function getEvents()
    {
        return $this->getContainer()->make('events');
    }
}
