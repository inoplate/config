<?php

namespace Inoplate\Config\Laravel;

use Inoplate\Config\Repositories\Config as ConfigRepository;

class EloquentConfig implements ConfigRepository
{
    /**
     * @var Config
     */
    protected $model;

    /**
     * Create new EloquentConfig instance
     * 
     * @param Config $model
     */
    public function __construct(Config $model)
    {
        $this->model = $model;
    }

    /**
     * Get config of given key
     * 
     * @param  string $key
     * @return object
     */
    public function get($key)
    {
        return $this->model->find($key);
    }

    /**
     * Save config of given key
     * 
     * @param  string $key
     * @param  string $value
     * @param  string $type
     * @return void
     */
    public function save($key, $value, $type)
    {
        $config = $this->model->firstOrCreate(['key' => $key]);
        $config->value = $value;
        $config->type = $type;

        $config->save();
    }

    /**
     * Retrieve all config
     * 
     * @return array
     */
    public function all()
    {
        return $this->model->all();
    }
}
