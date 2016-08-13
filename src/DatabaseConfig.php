<?php

namespace Inoplate\Config;

use Illuminate\Contracts\Cache\Repository as Cache;
use Inoplate\Config\Repositories\Config as ConfigRepository;

class DatabaseConfig implements Config
{
    /**
     * @var Inoplate\Config\Repositories\Config
     */
    protected $config;

    /**
     * @var Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create new DatabaseConfig instance
     * 
     * @param ConfigRepository $config
     * @param Cache            $cache
     */
    public function __construct(ConfigRepository $config, Cache $cache)
    {
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->get($key) ? true : false;
    }

    /**
     * Get the specified configuration value.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $cacheKey = $this->getCacheKey($key);

        $cached = $this->cache->get($cacheKey);

        $result = $cached ?: $this->config->get($key);
        $value = $result ? $result->value : null;
        $type = $result ? $result->type : null;

        return $value ? $type === 'serialized' ? unserialize($value) : $value : $default;
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        $cacheKey = $this->getCacheKey('all');

        $cached = $this->cache->get($cacheKey);
        $results = $cached ?: $this->config->all();
        $pivots = [];

        foreach ($results as $key => $value) {
            $pivots[$value->key] = $value->type === 'serialized' ? unserialize($value->value) : $value->value;
        }

        return $pivots;
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed  $value
     * @param  int|Carbon|null  $expiration
     * @return void
     */
    public function set($key, $value = null, $expiration = null)
    {
        $cacheKey = $this->getCacheKey($key);

        $type = is_array($value) || is_object($value) ? 'serialized' : 'unserialized';
        $value = $type === 'serialized' ? serialize($value) : $value;
        $object = $this->buildStdObject($key, $value, $type);

        $this->config->save($key, $value, $type);
        $this->cache->put($cacheKey, $object, $expiration ?: 10);

        // invalidate cache all
        $cacheKey = $this->getCacheKey('all');
        $this->uncacheAll();
    }

    /**
     * Cache all configuration
     *
     * @param int|Carbon|null $expiration
     * @return void
     */
    public function cacheAll($expiration = null)
    {
        $cacheKey = $this->getCacheKey('all');
        $results = $this->config->all();

        if($expiration === -1) {
            $this->cache->forever($cacheKey, $results);
        }else {
            $this->cache->put($cacheKey, $results, $expiration ?: 10);
        }
    }

    /**
     * Uncache all configuration
     * 
     * @return void
     */
    public function uncacheAll()
    {
        $cacheKey = $this->getCacheKey('all');

        $this->cache->forget($cacheKey);
    }

    /**
     * Determine if all config were cached
     * 
     * @return boolean
     */
    public function isAllCached()
    {
        $cacheKey = $this->getCacheKey('all');

        return $this->cache->has($cacheKey);
    }

    /**
     * Build StdObject
     * 
     * @param  string $key
     * @param  mixed $value
     * @param  string $type
     * @return StdClass
     */
    protected function buildStdObject($key, $value, $type)
    {
        $object = new \StdClass;

        $object->key = $key;
        $object->value = $value;
        $object->type = $type;

        return $object;
    }

    /**
     * Retrieve cache key
     * 
     * @param  string $key
     * @return string
     */
    protected function getCacheKey($key)
    {
        return md5('config::' . $key);
    }
}
