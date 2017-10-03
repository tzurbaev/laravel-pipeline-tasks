<?php

namespace Zurbaev\PipelineTasks\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        if (!is_null(($container = \Mockery::getContainer()))) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }

        \Mockery::close();
    }
}
