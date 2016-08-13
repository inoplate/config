<?php

namespace spec\Inoplate\Config\Laravel;

use Inoplate\Config\Laravel\DatabaseConfigRepository;
use Inoplate\Config\Config as InoplateConfig;
use Illuminate\Contracts\Config\Repository as LaravelConfig;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;

class DatabaseConfigRepositorySpec extends LaravelObjectBehavior
{
    function let(InoplateConfig $inoplateConfig)
    {
        $inoplateConfig->all()->shouldBeCalled()->willReturn(['ahc' => 'def']);
        $this->beConstructedWith($inoplateConfig, []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DatabaseConfigRepository::class);
    }

    function it_should_be_a_laravel_config()
    {
        $this->shouldImplement('Illuminate\Contracts\Config\Repository');
    }

    function it_should_be_an_array_access()
    {
        $this->shouldImplement(\ArrayAccess::class);
    }

    function it_decorate_inoplate_config(InoplateConfig $inoplateConfig)
    {
        $inoplateConfig->set('app.name', 'My Application')->shouldBeCalled();
        $this->set('app.name', 'My Application');
    }
}
