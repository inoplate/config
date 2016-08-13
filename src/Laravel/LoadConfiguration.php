<?php

namespace Inoplate\Config\Laravel;

use Illuminate\Config\Repository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\DatabaseManager;
use Inoplate\Config\Laravel\Config;
use Inoplate\Config\Laravel\EloquentConfig;
use Inoplate\Config\Laravel\DatabaseConfigRepository;
use Inoplate\Config\DatabaseConfig;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Cache\FileStore;
use Illuminate\Filesystem\Filesystem;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $items = [];

        // First we will see if we have a cache configuration file. If we do, we'll load
        // the configuration items from that file so that it is very quick. Otherwise
        // we will need to spin through every configuration file and load them all.
        if (file_exists($cached = $app->getCachedConfigPath())) {
            $items = require $cached;

            $loadedFromCache = true;
        }        

        // Load config first
        $app->instance('config', $config = new Repository($items));

        // Next we will spin through all of the configuration files in the configuration
        // directory and load each one into the repository. This will make all of the
        // options available to the developer for use in various parts of this app.
        if (! isset($loadedFromCache)) {
            $this->loadConfigurationFiles($app, $config);

            $databaseRepository = $this->getDatabaseRepository($app);
            // Database has been setted up
            if($databaseRepository) {
                $app->instance('config', new DatabaseConfigRepository($databaseRepository, $config->all()));
            }
        }

        $app->detectEnvironment(function () use ($config) {
            return $config->get('app.env', 'production');
        });

        date_default_timezone_set($config['app.timezone']);

        mb_internal_encoding('UTF-8');
    }

    /**
     * Load the configuration items from all of the files.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  \Illuminate\Contracts\Config\Repository  $repository
     * @return void
     */
    protected function loadConfigurationFiles(Application $app, RepositoryContract $repository)
    {
        foreach ($this->getConfigurationFiles($app) as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    /**
     * Get all of the configuration files for the application.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return array
     */
    protected function getConfigurationFiles(Application $app)
    {
        $files = [];

        $configPath = realpath($app->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $nesting = $this->getConfigurationNesting($file, $configPath);

            $files[$nesting.basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @param  string  $configPath
     * @return string
     */
    protected function getConfigurationNesting(SplFileInfo $file, $configPath)
    {
        $directory = dirname($file->getRealPath());

        if ($tree = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $tree = str_replace(DIRECTORY_SEPARATOR, '.', $tree).'.';
        }

        return $tree;
    }

    /**
     * Retrieve config from DB
     * 
     * @param  Application $app
     * @return DatabaseConfig
     */
    protected function getDatabaseRepository(Application $app)
    {
        $connectionFactory = new ConnectionFactory($app);
        $resolver = new DatabaseManager($app, $connectionFactory);
        $schema = $resolver->connection()->getSchemaBuilder();

        if(!$schema->hasTable('configs')) {
            return null;
        }

        $config = $this->setUpConfigModel($app, $resolver);
        $configRepository = new EloquentConfig($config);

        $cache = $this->setUpCache($app);
        return new DatabaseConfig($configRepository, $cache);
    }

    /**
     * Setting up config model
     * 
     * @param Application          $app
     * @param DatabaseManager      $resolver
     * @return Config
     */
    protected function setUpConfigModel(Application $app, $resolver)
    {
        $config = new Config;
        $config->setConnectionResolver($resolver);

        return $config;
    }

    /**
     * Setting up cache
     * 
     * @param Application $app
     * @return Illuminate\Cache\Repository
     */
    protected function setUpCache(Application $app)
    {
        $filesystem = new Filesystem;
        $fileStore =  new FileStore($filesystem, $app['config']->get('cache.stores.file.path'));
        $repository = new Cache($fileStore);

        return $repository;
    }
}
