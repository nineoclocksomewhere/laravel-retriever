<?php

namespace Nocs\Retriever\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Retriever facade class
 */
class Retriever extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'retriever';
    }

}