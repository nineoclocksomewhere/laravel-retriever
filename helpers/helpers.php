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

        if (func_num_args()) {

            return call_user_func_array([app('retriever'), 'get'], func_get_args());

        } else {

            return app('retriever');

        }

    }
}
