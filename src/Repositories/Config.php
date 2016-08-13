<?php

namespace Inoplate\Config\Repositories;

interface Config
{
    /**
     * Get config of given key
     * 
     * @param  string $key
     * @return object
     */
    public function get($key);

    /**
     * Save config of given key
     * 
     * @param  string $key
     * @param  string $value
     * @param  string $type
     * @return void
     */
    public function save($key, $value, $type);

    /**
     * Retrieve all config
     * 
     * @return array
     */
    public function all();
}
