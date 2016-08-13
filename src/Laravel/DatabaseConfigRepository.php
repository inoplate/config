<?php

namespace Inoplate\Config\Laravel;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as Contract;
use Inoplate\Config\Config as InoplateConfig;

class DatabaseConfigRepository implements ArrayAccess, Contract
{
    /**
     * @var Illuminate\Contracts\Config\Repository
     */
    protected $laravelConfig;

    /**
     * @var Inoplate\Config\Config
     */
    protected $inoplateConfig;

    /**
     * Create new DatabaseConfigRepository instance
     * 
     * @param Contract       $laravelConfig
     * @param InoplateConfig $inoplateConfig
     */
    public function __construct(Contract $laravelConfig, InoplateConfig $inoplateConfig)
    {
        $this->laravelConfig = $laravelConfig;
        $this->inoplateConfig = $inoplateConfig;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->laravelConfig->has($key);
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
        return $this->laravelConfig->get($key, $default);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->laravelConfig->all();
    }

    /**
     * Set a given configuration value.
     *
     * @param  array|string  $key
     * @param  mixed   $value
     * @return void
     */
    public function set($key, $value = null)
    {
        $this->inoplateConfig->set($key, $value);
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $this->laravelConfig->prepend($key, $value);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $this->laravelConfig->push($key, $value);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->laravelConfig->offsetSet($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->laravelConfig->offsetUnset($key);
    }
}
