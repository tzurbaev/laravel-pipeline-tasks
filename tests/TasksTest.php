<?php

namespace Zurbaev\PipelineTasks\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Zurbaev\PipelineTasks\Events\PipelineTaskCompleted;
use Zurbaev\PipelineTasks\Events\PipelineTaskFailed;
use Zurbaev\PipelineTasks\TaskManager;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatFails\ExampleTaskThatFails;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\ExampleTaskThatSucceeds;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes\FirstPipe;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes\SecondPipe;
use Zurbaev\PipelineTasks\Tests\Stubs\Tasks\TaskThatSucceeds\Pipes\ThirdPipe;

class TasksTest extends TestCase
{
    /**
     * @var TaskManager
     */
    protected $manager;

    public function setUp()
    {
        parent::setUp();

        $this->manager = new TaskManager(
            Container::getInstance()
        );
    }

    public function testCompletedCallbackShouldBeCalledOnSuccess()
    {
        $task = \Mockery::mock(ExampleTaskThatSucceeds::class.'[completed]');
        $task->shouldReceive('completed')->once();

        $this->manager->start($task);
    }

    public function testCompletedEventShouldBeFiredOnSuccess()
    {
        $events = \Mockery::spy(Dispatcher::class);
        $events->shouldReceive('dispatch')->atLeast()->once()->andReturnUsing(function ($called) {
            $this->assertInstanceOf(PipelineTaskCompleted::class, $called);
        });

        $manager = \Mockery::mock(TaskManager::class.'[hasEvents,getEvents]', [Container::getInstance()])
            ->shouldAllowMockingProtectedMethods();
        $manager->shouldReceive('hasEvents')->atLeast()->once()->andReturn(true);
        $manager->shouldReceive('getEvents')->atLeast()->once()->andReturn($events);

        $task = new ExampleTaskThatSucceeds();

        $manager->start($task);
    }

    public function testFailedCallbackShouldBeCalledOnFailure()
    {
        $task = \Mockery::mock(ExampleTaskThatFails::class.'[failed]');
        $task->shouldReceive('failed')->once();

        $this->manager->start($task);
    }

    public function testFailedCallbackShouldReceiveException()
    {
        $task = new ExampleTaskThatFails();

        $result = $this->manager->start($task);

        $this->assertSame('ExceptionThrownFromFirstPipe', $result);
    }

    public function testFailedEventShouldBeFiredOnFailure()
    {
        $events = \Mockery::spy(Dispatcher::class);
        $events->shouldReceive('dispatch')->atLeast()->once()->andReturnUsing(function ($called) {
            $this->assertInstanceOf(PipelineTaskFailed::class, $called);
        });

        $manager = \Mockery::mock(TaskManager::class.'[hasEvents,getEvents]', [Container::getInstance()])
            ->shouldAllowMockingProtectedMethods();
        $manager->shouldReceive('hasEvents')->atLeast()->once()->andReturn(true);
        $manager->shouldReceive('getEvents')->atLeast()->once()->andReturn($events);

        $task = new ExampleTaskThatFails();

        $manager->start($task);
    }

    public function testPipeResultShouldBeStoredInTaskResults()
    {
        $task = new ExampleTaskThatSucceeds();
        $this->manager->start($task);

        $this->assertTrue($task->hasPipeResult('FirstPipe'));
        $this->assertSame('FirstPipeResultFromTaskThatSucceeds', $task->getPipeResult('FirstPipe'));
    }

    public function testPipeMethodCanBeOverriden()
    {
        $task = \Mockery::mock(ExampleTaskThatSucceeds::class.'[pipeMethod]');
        $task->shouldReceive('pipeMethod')->once()->andReturn('overridenHandleMethod');
        $this->manager->start($task);

        $this->assertTrue($task->hasPipeResult('FirstPipe'));
        $this->assertSame('FirstPipeResultFromTaskThatSucceedsWithOverridenMethod', $task->getPipeResult('FirstPipe'));
    }

    public function testPipeNameCanBeOverriden()
    {
        $pipe = \Mockery::mock(FirstPipe::class.'[name]');
        $pipe->shouldReceive('name')->atLeast()->once()->andReturn('overridenFirstPipe');

        $task = \Mockery::mock(ExampleTaskThatSucceeds::class.'[pipes]');
        $task->shouldReceive('pipes')->once()->andReturn([
            $pipe,
        ]);

        $this->manager->start($task);

        $this->assertFalse($task->hasPipeResult('FirstPipe'));
        $this->assertTrue($task->hasPipeResult('overridenFirstPipe'));
    }

    public function testTaskInstanceShouldBeAvailableOnPipeObject()
    {
        $task = \Mockery::mock(ExampleTaskThatSucceeds::class.'[pipes]');
        $task->shouldReceive('pipes')->once()->andReturn([
            FirstPipe::class,
            new ThirdPipe(),
        ]);

        $this->manager->start($task);

        $this->assertTrue($task->hasPipeResult('ThirdPipe'));
        $this->assertSame('TaskAvailable', $task->getPipeResult('ThirdPipe'));
    }

    public function testDependenciesShouldBeInjectedToTheHandleMethod()
    {
        $task = \Mockery::mock(ExampleTaskThatSucceeds::class.'[pipes]');
        $task->shouldReceive('pipes')->once()->andReturn([
            FirstPipe::class,
            SecondPipe::class,
        ]);

        $this->manager->start($task);

        $this->assertTrue($task->hasPipeResult('SecondPipe'));
        $this->assertSame('InjectedExampleValue', $task->getPipeResult('SecondPipe'));
    }
}
