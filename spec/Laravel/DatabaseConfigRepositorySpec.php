<?php

namespace spec\Inoplate\Config\Laravel;

use Inoplate\Config\Laravel\DatabaseConfigRepository;
use Inoplate\Config\Config as InoplateConfig;
use Illuminate\Contracts\Config\Repository as LaravelConfig;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;

class DatabaseConfigRepositorySpec extends LaravelObjectBehavior
{
    function let(LaravelConfig $laravelConfig, InoplateConfig $inoplateConfig)
    {
        $this->beConstructedWith($laravelConfig, $inoplateConfig);
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

    function it_decorate_laravel_config(LaravelConfig $laravelConfig)
    {
        $laravelConfig->has('app.name')->shouldBeCalled()->willReturn(true);
        $this->has('app.name')->shouldReturn(true);

        $laravelConfig->get('app.name', null)->shouldBeCalled()->willReturn('My Application');
        $this->get('app.name')->shouldReturn('My Application');

        $laravelConfig->all()->shouldBeCalled()->willReturn(['app' => ['name' => 'My Application']]);
        $this->all()->shouldReturn(['app' => ['name' => 'My Application']]);

        $laravelConfig->prepend('app.name', 'My Application')->shouldBeCalled();
        $this->prepend('app.name', 'My Application');

        $laravelConfig->push('app.name', 'My Application')->shouldBeCalled();
        $this->push('app.name', 'My Application');
    }

    function it_decorate_inoplate_config(InoplateConfig $inoplateConfig)
    {
        $inoplateConfig->set('app.name', 'My Application')->shouldBeCalled();
        $this->set('app.name', 'My Application');
    }
}
