<?php

namespace Nocs\Retriever\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
     * [$callers description]
     * @var array
     */
    protected $callers = [];

    /**
     * [$paths description]
     * @var array
     */
    protected $paths = [];

    /**
     * [__construct description]
     * @param [type] $app [description]
     */
    public function __construct($app = null)
    {
        $this->app = $app;
    }

    /**
     * [registerRetrievers description]
     * @param  [type] $namespace [description]
     * @return [type]            [description]
     */
    public function loadRetrieversFrom($path, $namespace = null)
    {

        $namespace = Str::snake($namespace) ?? 'app.cache';

        if (is_dir($path)) {

            $path = realpath($path);

            if (!isset($this->paths[$namespace])) {

                $this->paths[$namespace] = [];

                // keep app.cache first and app.retrievers second
                uksort($this->paths, function($a, $b) {
                    //fl($a.'-'.$b);
                    if ($b == 'app.retrievers') { // keep app.retrievers first
                        return 1;
                    } elseif ($b == 'app.cache' && $a != 'app.retrievers') { // keep app.cache second
                        return 1;
                    } else {
                        return 0;
                    }
                });
            }

            if (!in_array($path, $this->paths[$namespace])) {
                $this->paths[$namespace][] = $path;
            }
        }

        return $this;
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

        if (empty($ttl)) {
            $ttl = null;
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

    public function forget($key, $parameters = []): void
    {

        $cacheKey = $this->cacheKey($key, $parameters);

        Cache::forget($cacheKey);
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
     * [splitName description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function splitName($name)
    {

        $caller = $name;

        $namespace = 'app';
        if (preg_match('/^([^\:]+)\:\:(.+)$/', $caller, $m)) {
            $namespace = $m[1];
            $caller = $m[2];
        }

        $method = 'get';
        if (preg_match('/^([^\.]+)\.(.+)$/', $caller, $m)) {
            $caller = $m[1];
            $method = $m[2];
        }

        $key = ($namespace ? $namespace.'.' : '') . $caller;

        return [
            'key' => $key,
            'namespace' => $namespace,
            'caller' => $caller,
            'method' => Str::camel($method),
        ];
    }

    /**
     * [caller description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function caller($name)
    {

        $split = $this->splitName($name);
        if (array_key_exists($split['key'], $this->callers)) {
            return $this->callers[$split['key']];
        }

        foreach ($this->paths as $namespace => $paths) {

            if (!empty($split['namespace']) && ($split['namespace'] != $namespace)) {
                continue;
            }

            foreach ($paths as $path) {
                foreach (File::files($path) as $file) {
                    if ($class = $this->getClassFromFile($file)) {
                        $cache = new $class;
                        $callerKey = $namespace.'.'.Str::snake((new \ReflectionClass($cache))->getShortName());
                        $this->callers[$callerKey] = $cache;
                    }
                }
            }

            if (array_key_exists($split['key'], $this->callers)) {
                return $this->callers[$split['key']];
            }
        }

        return ($this->callers[$split['key']] = null);
    }

    /**
     * [getClassFromFile description]
     * @param  [type] $file [description]
     * @return [type]       [description]
     */
    public function getClassFromFile($file)
    {

        $namespace = null;
        $class = null;

        if ($fp = fopen($file->getPath().'/'.$file->getFilename(), 'r')) {
            while ($line = fgets($fp, 4096)) {
                if (preg_match('/^\s*namespace\s+([A-Za-z_0-9\\\]+)/s', $line, $m)) {
                    $namespace = $m[1];
                } elseif (preg_match('/^\s*([a-z]+\s+)*class\s+([A-Za-z_0-9]+)/s', $line, $m)) {
                    $class = $m[2];
                }
                if (!empty($class)) {
                    break;
                }
            }
            fclose($fp);
        }

        $class = !empty($class) ? (!empty($namespace) ? $namespace.'\\' : '') . $class : null;

        return class_exists($class) ? $class : null;
    }

    /**
     * [callable description]
     * @param  [type] $key [description]
     * @return [type]      [description]
     */
    public function callable($name)
    {

        if (!($caller = $this->caller($name))) {
            return null;
        }

        $split = $this->splitName($name);
        if (!method_exists($caller, $split['method'])) {
            return null;
        }

        return (object) [
            'caller' => $caller,
            'method' => $split['method'],
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
