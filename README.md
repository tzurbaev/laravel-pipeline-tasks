# Pipeline Tasks Helper

This package allows you to create some tasks with list of steps (pipes) and execute that steps one-by-one with ability to interrupt task execution on any step.

[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]
[![ScrutinizerCI][ico-scrutinizer]][link-scrutinizer]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

## Requirements

This package requires PHP 7.1 or higher.

## Installation

You can install the package via composer:

``` bash
$ composer require tzurbaev/laravel-pipeline-tasks
```

## Documentation

### Examples

Example task & pipes are defined in the `examples` directory. Once you define your tasks,
you can start your tasks via the TasksManager class:

```php
<?php

use App\CloneGitRepositoryTask;
use Zurbaev\PipelineTasks\TaskManager;
use Illuminate\Container\Container;

$manager = new TaskManager(Container::getInstance());
$task = new CloneGitRepositoryTask(
    'git@github.com:tzurbaev/laravel-pipeline-tasks.git',
    'master',
    '/var/git/pipeline-tasks'
);

$manager->start($task);
```

Or you can use `PipelineTasks` facade:

```php
<?php

use App\CloneGitRepositoryTask;
use Zurbaev\PipelineTasks\Facades\PipelineTasks;

$task = new CloneGitRepositoryTask(
    'git@github.com:tzurbaev/laravel-pipeline-tasks.git',
    'master',
    '/var/git/pipeline-tasks'
);

PipelineTasks::start($task);
```

All pipes will be executed in the order they are defined in the `pipes` method.

### Stop execution

If you need to stop further task pipes from execution, just return `false` from your `handle` method.

Please note that there's strict type checking for the `false` value. This means that if you return empty string, `0` or `null` or any other falsish value, task execution won't be stopped.

### Skip pipe

If you need to skip some pipe, just return any non-false value in the beginning of the `handle` method (or in any reasonable place).

You can also return some value that can be examined lately in the `completed` or `failed` methods of your task.

### Accessing pipes results

`Task` class provides helper methods to retrieve any pipe's results:

- `hasPipeResult(string $name)` - determines if task has result for the given pipe name;
- `getPipeResult(string $name, $default = null)` - returns pipe value or default value if empty;
- `results()` - returns array of all pipes results.

### Chaning pipe name

If you have several pipes of the same class, you might want to override its names, since at the end of the task you'll have results from the latest similar pipe.

You can override `public function name()` in you pipe class:

```php
<?php

namespace App\Pipes;

use App\CloneGitRepositoryTask;
use Zurbaev\PipelineTasks\Pipe;

class CloneGitRepository extends Pipe
{
    /**
     * @var CloneGitRepositoryTask
     */
    protected $task;

    public function name()
    {
        return 'clone-git-branch-'.$this->task->getBranch();
    }

    public function handle()
    {
        // Handle pipe.
    }
}
```

Now results will be stored as `clone-git-branch-master` instead of `CloneGitRepository`.

### Events

`TaskManager` fires two events: `Zurbaev\PipelineTasks\Events\PipelineTaskCompleted` in case of successful task execution
and `Zurbaev\PipelineTasks\Events\PipelineTaskFailed` in case of failure.

`PipelineTaskCompleted` instance contains `public $task` property, so you can access it in your listeners.
`PipelineTaskFailed` in addition to the `public $task` property provides `public $exception` for identification what exact pipe caused failure.

### Exceptions

If something went wrong during task execution, you can detect it via `failed` method on your task class or via listening to the `PipelineTaskFailed` event.

`failed` method accepts `Zurbaev\PipelineTasks\Exceptions\PipelineTaskFailedException` instance as first argument, so you can inspect it.

You can use `$e->getPipeName()` method to get the failed pipe's name.

If there was an exception while running pipe, you can access it via `$e->getPrevious()` method.

Exception codes described as constants of `PipelineTaskFailedException` class:

- `PipelineTaskFailedException::EXCEPTION_THROWN` - means that there was an exception while running pipe. You can access this exception via `$e->getPrevious()` method;
- `PipelineTaskFailedException::REJECTED` - means that pipe return `false`, so task has been marked as failed.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email zurbaev@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://poser.pugx.org/tzurbaev/laravel-pipeline-tasks/version?format=flat
[ico-license]: https://poser.pugx.org/tzurbaev/laravel-pipeline-tasks/license?format=flat
[ico-travis]: https://api.travis-ci.org/tzurbaev/laravel-pipeline-tasks.svg?branch=master
[ico-styleci]: https://styleci.io/repos/XXX/shield?branch=master&style=flat
[ico-scrutinizer]: https://scrutinizer-ci.com/g/tzurbaev/laravel-pipeline-tasks/badges/quality-score.png?b=master

[link-packagist]: https://packagist.org/packages/tzurbaev/laravel-pipeline-tasks
[link-travis]: https://travis-ci.org/tzurbaev/laravel-pipeline-tasks
[link-styleci]: https://styleci.io/repos/XXX
[link-scrutinizer]: https://scrutinizer-ci.com/g/tzurbaev/laravel-pipeline-tasks/
[link-author]: https://github.com/tzurbaev
