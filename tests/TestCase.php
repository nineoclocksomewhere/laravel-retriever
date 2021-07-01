<?php

namespace Nocs\Retriever\Tests;

use Nocs\Retriever\Providers\RetrieverServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        retriever()->loadRetrieversFrom(__dir__.'/Cache', 'retriever');

    }

    protected function getPackageProviders($app)
    {
        return [
            RetrieverServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

        // ...

    }

}
