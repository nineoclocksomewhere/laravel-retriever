<?php

namespace Nocs\Retriever\Support;
use Illuminate\Support\Traits\ForwardsCalls;

class RetrieverManager
{

    use ForwardsCalls;

    /**
     * [$app description]
     * @var [type]
     */
    protected $app;

    /**
     * [__construct description]
     * @param [type] $app [description]
     */
    public function __construct($app = null)
    {

        $this->app = $app;

    }

    /**
     * Retrieve the data from cache
     * @param  string $key        classname/method combo key
     * @param  array  $parameters optional parameters to pass to the callback
     * @param  int    $ttl        time to live in seconds
     * @return mixed              data retrieved from cache
     */
    public function get($key, $parameters = [], $ttl = null)
    {

        if (is_null($ttl)) {
            $ttl = $this->mediumTime();
        }

        // ...

        return null;
    }

    /**
     * Get the times in seconds
     *
     * @return array Times
     */
    public function times(): array
    {
        return config('cache.retriever_times', [
            'short'  => env('RETRIEVER_TIME_SHORT',    900), // 15 minutes
            'medium' => env('RETRIEVER_TIME_MEDIUM',  3600), // 1 hour
            'long'   => env('RETRIEVER_TIME_LONG',   86400), // 24 hours
        ]);
    }

    /**
     * [__call description]
     * @param  [type] $method     [description]
     * @param  [type] $parameters [description]
     * @return [type]             [description]
     */
    public function __call($method, $parameters)
    {

        if (preg_match('/^([a-z]+)Time$/', $method, $m)) {
            $times = $this->times();
            return $times[$m[1]] ?? ($times['medium'] ?? 3600);
        }

        static::throwBadMethodCallException($method);
    }

}
