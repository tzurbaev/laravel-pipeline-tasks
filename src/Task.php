<?php

namespace Zurbaev\PipelineTasks;

use Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException;

abstract class Task
{
    /**
     * Pipe handlers results for current task.
     *
     * @var array
     */
    protected $results = [];

    /**
     * Method name to be executed on each pipe.
     *
     * @var string
     */
    protected $pipeMethod = 'process';

    /**
     * Get the pipes list.
     *
     * @return array
     */
    abstract public function pipes();

    /**
     * Completed task handler.
     *
     * @return mixed
     */
    public function completed()
    {
        return true;
    }

    /**
     * Failed task handler.
     *
     * @param PipelineTaskFailedException $exception
     *
     * @return mixed
     */
    public function failed(PipelineTaskFailedException $exception)
    {
        return false;
    }

    /**
     * Get the pipe method.
     *
     * @return string
     */
    public function pipeMethod()
    {
        return $this->pipeMethod;
    }

    /**
     * Store pipe result.
     *
     * @param string $name
     * @param mixed  $result
     *
     * @return $this
     */
    public function setPipeResult(string $name, $result)
    {
        $this->results[$name] = $result;

        return $this;
    }

    /**
     * Determines if result for the given pipe already exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasPipeResult(string $name)
    {
        return isset($this->results[$name]);
    }

    /**
     * Get pipe result.
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getPipeResult(string $name, $default = null)
    {
        return $this->hasPipeResult($name) ? $this->results[$name] : $default;
    }

    /**
     * Get all available results.
     *
     * @return array
     */
    public function results()
    {
        return $this->results;
    }
}
