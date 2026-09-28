<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // ponytail: www-data-owned storage/framework/views unwritable here; compile to tmp.
        $this->afterApplicationCreated(function () {
            config()->set('view.compiled', '/tmp/heru-test-views');
            @mkdir('/tmp/heru-test-views', 0777, true);
        });
        $this->app['config']->set('view.compiled', '/tmp/heru-test-views');
        $this->app['config']->set('logging.default', 'errorlog');
        @mkdir('/tmp/heru-test-views', 0777, true);
    }
}
