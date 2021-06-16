<?php

namespace Nocs\Retriever\Support;

class RetrieverManager
{

    /**
     * [$app description]
     * @var [type]
     */
    protected $app;

    /**
     * [__construct description]
     * @param [type] $app [description]
     */
    public function __construct($app = null) {

        $this->app = $app;

    }

}