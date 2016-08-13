<?php

namespace spec\Inoplate\Config\Laravel;

use Inoplate\Config\Laravel\LoadConfiguration;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LoadConfigurationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LoadConfiguration::class);
    }
}
