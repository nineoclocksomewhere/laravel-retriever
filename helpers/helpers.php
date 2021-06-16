<?php

if (! function_exists('retriever')) {
    /**
     * retriever helper
     *
     * @param  dynamic  null
     * @return mixed|\Nocs\Retriever\Support\RetrieverManager
     *
     * @throws \Exception
     */
    function retriever()
    {
        return app('retriever');
    }
}
