<?php

namespace spec\Inoplate\Config\Laravel;

use Inoplate\Config\Laravel\ConfigServiceProvider;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;
use Illuminate\Foundation\Application;

class ConfigServiceProviderSpec extends LaravelObjectBehavior
{
    function let(Application $laravel)
    {
        $this->beConstructedWith($laravel);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ConfigServiceProvider::class);
    }
}
