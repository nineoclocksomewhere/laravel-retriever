<?php

namespace Nocs\Retriever\Support;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class RetrieverManager
{

    use ForwardsCalls;

    /**
     * [$app description]
     * @var [type]
     */
    protected $app;

    /**
     * [$callers description]
     * @var array
     */
    protected static $callers = [];

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

        if (!($callable = $this->callable($key))) {
            return null;
        }

        $cacheKey = $this->cacheKey($key, $parameters);

        return Cache::remember($cacheKey, $ttl, function () use ($callable, $parameters) {
            return call_user_func_array([$callable->caller, $callable->method], $parameters);
        });
    }

    /**
     * [cacheKey description]
     * @param  [type] $key        [description]
     * @param  array  $parameters [description]
     * @return [type]             [description]
     */
    public function cacheKey($key, $parameters = []): string
    {
        return 'retriever.' . $key . (!empty($parameters) ? '.' . md5(serialize($parameters)) : '');
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
     * [caller description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public static function caller($name)
    {

        if (preg_match('/^([^\.]+)\..+$/', $name, $m)) {
            $name = $m[1];
        }

        if (array_key_exists($name, static::$callers)) {
            return static::$callers[$name];
        }

        $class = 'App\\Cache\\' . Str::studly($name);
        if (!class_exists($class)) {
            return (static::$callers[$name] = null);
        }

        return (static::$callers[$name] = new $class);
    }

    /**
     * [callable description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public static function callable($key)
    {

        if (!preg_match('/^([^\.]+)\.(.+)$/', trim($key), $m)) {
            return null;
        }

        if (!($caller = static::caller($m[1]))) {
            return null;
        }

        $method = Str::camel($m[2]);
        if (!method_exists($caller, $method)) {
            return null;
        }

        return (object) [
            'caller' => $caller,
            'method' => $method,
        ];
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
