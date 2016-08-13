<?php

namespace Inoplate\Config\Laravel;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as Contract;
use Illuminate\Config\Repository as LaraveConfig;
use Inoplate\Config\Config as InoplateConfig;

class DatabaseConfigRepository extends LaraveConfig
{
    /**
     * @var Inoplate\Config\Config
     */
    protected $inoplateConfig;

    /**
     * Create new DatabaseConfigRepository instance
     * 
     * @param InoplateConfig $inoplateConfig
     */
    public function __construct(InoplateConfig $inoplateConfig, array $items = [])
    {
        parent::__construct($items);

        $this->inoplateConfig = $inoplateConfig;
        $this->overWrite($inoplateConfig);
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
     * Overwirite existing config by database value
     * 
     * @param  InoplateConfig $inoplateConfig
     * @return void
     */
    protected function overWrite(InoplateConfig $inoplateConfig)
    {
        $config = $inoplateConfig->all();

        // Overrider from file configuration
        foreach ($config as $key => $value) {
            parent::set($key, $value);
        }
    }
}
