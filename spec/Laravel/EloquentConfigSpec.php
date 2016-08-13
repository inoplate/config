<?php

namespace spec\Inoplate\Config\Laravel;

use Inoplate\Config\Laravel\EloquentConfig;
use Inoplate\Config\Laravel\Config;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;
use Artisan;
use StdClass;

class EloquentConfigSpec extends LaravelObjectBehavior
{
    protected $configRepository;

    function let()
    {
        Artisan::call('migrate');

        $config = new Config;
        $this->beConstructedWith($config);

        $this->setUpValues();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(EloquentConfig::class);
    }

    function it_should_be_a_config_repository()
    {
        $this->shouldImplement('Inoplate\Config\Repositories\Config');
    }

    function it_return_config_of_given_key()
    {
        $config = Config::find('regular_value');
        $this->get('regular_value')->shouldBeLike($config);

        $config = Config::find('array_value');
        $this->get('array_value')->shouldBeLike($config);

        $config = Config::find('object_value');
        $this->get('object_value')->shouldBeLike($config);
    }

    function it_can_save_value()
    {
        $expected = Config::find('regular_value');
        $expected->value = serialize(['abc', 'def', 'ghi']);
        $expected->type = 'serialized';

        $this->save('regular_value', $expected->value, $expected->type);
        $this->get('regular_value')->shouldHaveTypeOfData('serialized');
        $this->get('regular_value')->shouldHaveValue($expected->value);
    }

    function it_can_get_all_values()
    {
        $expected = Config::all();

        $this->all()->shouldBeLike($expected);
    }

    function letGo()
    {
        Artisan::call('migrate:reset');
    }

    public function getMatchers()
    {
        return [
            'haveTypeOfData' => function ($subject, $type) {
                return $type == $subject->type;
            },
            'haveValue' => function ($subject, $value) {
                return $value == $subject->value;
            },
        ];
    }

    private function setUpValues()
    {
        $config = new Config;
        $config->key = 'regular_value';
        $config->value = 'regular value';
        $config->type = 'unserialized';
        $config->save();

        $config = new Config;
        $config->key = 'array_value';
        $config->value = serialize([ 'abc' => ['def'], 'ghi', 'jkl' ]);
        $config->type = 'serialized';
        $config->save();

        $obj = new StdClass;
        $obj->abc = 'def';
        $obj->ghi = ['jkl', 'mno' => ['pqr']];

        $config = new Config;
        $config->key = 'object_value';
        $config->value = serialize($obj);
        $config->type = 'serialized';
        $config->save();
    }
}
