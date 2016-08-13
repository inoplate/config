<?php

use PHPUnit\Framework\TestCase;
use Inoplate\Config\Laravel\Config;

class IntegrationTest extends TestCase
{
    protected $app;

    public function setUp()
    {
        $this->app = require __DIR__."/../bootstrap/app.php";
        $this->app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        Artisan::call('migrate');
        $this->setUpDb();

        // Reboot because table already created
        $this->app = require __DIR__."/../bootstrap/app.php";
        $this->app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    }

    public function testIfConfigBindigToDatabaseRepository()
    {
        $this->assertEquals(
            get_class($this->app['config']), Inoplate\Config\Laravel\DatabaseConfigRepository::class
        );
    }

    public function testConfigReturnFromDatabase()
    {
        $this->assertEquals(
            $this->app['config']->get('app.name'), 'This is application name'
        );

        $this->assertEquals(
            $this->app['config']->get('mail.from'), ['address' => 'mail-db@from.com', 'name' => 'Mail form db']
        );
    }

    public function itCanPersistToDatabase()
    {
        $this->app['config']->set('app.name', 'My new application');
        $newConfig = Config::find('app.name');

        $this->assertEquals($newConfig->value, 'My new application');
    }

    public function tearDown()
    {
        Artisan::call('migrate:reset');
    }

    protected function setUpDb()
    {
        $config = new Config;
        $config->key = 'app.name';
        $config->value = 'This is application name';
        $config->type = 'unserialized';
        $config->save();

        $config = new Config;
        $config->key = 'mail.from';
        $config->value = serialize(['address' => 'mail-db@from.com', 'name' => 'Mail form db']);
        $config->type = 'serialized';
        $config->save();
    }
}