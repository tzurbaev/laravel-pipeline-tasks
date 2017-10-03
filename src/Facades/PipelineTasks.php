<?php

namespace Zurbaev\PipelineTasks\Facades;

use Illuminate\Support\Facades\Facade;
use Zurbaev\PipelineTasks\TaskManager;

/**
 * Class PipelineTasks
 *
 * @method static mixed start(\Zurbaev\PipelineTasks\Task $task)
 */
class PipelineTasks extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TaskManager::class;
    }
}
