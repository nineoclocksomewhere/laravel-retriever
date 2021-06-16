<?php

namespace Nocs\Retriever\Providers;

use Illuminate\Support\ServiceProvider;
use Nocs\Retriever\Support\RetrieverManager;

/**
 * BackpackServiceProvider class
 */
class RetrieverServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('retriever', function ($app) {
            return new RetrieverManager($app);
        });

    }

    /**
     * [boot description]
     * @return [type] [description]
     */
    public function boot()
    {

        // ...

        if ($this->app->runningInConsole()) {

            // ...

        }

    }

}