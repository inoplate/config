<?php

namespace spec\Inoplate\Config;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Inoplate\Config\DatabaseConfig;
use Inoplate\Config\Repositories\Config;
use Illuminate\Contracts\Cache\Repository as Cache;

class DatabaseConfigSpec extends ObjectBehavior
{
    function let(Config $repository, Cache $cache)
    {
        $this->beConstructedWith($repository, $cache);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DatabaseConfig::class);
    }

    function it_should_be_a_config_repository()
    {
        $this->shouldImplement('Inoplate\Config\Config');
    }

    function it_should_return_value_based_on_database(Config $repository)
    {
        $result = new \stdClass;
        $result->key = 'val';
        $result->value = 'value of val';
        $result->type = 'unserialized';

        $repository->get('val')->shouldBeCalled()->willReturn($result);

        $this->get('val')->shouldReturn('value of val');

        // null value without default value
        $repository->get('null_val')->shouldBeCalled()->willReturn(null);

        $this->get('null_val')->shouldReturn(null);

        // null value with default value
        $repository->get('val')->shouldBeCalled()->willReturn(null);

        $this->get('val', 'default value')->shouldReturn('default value');

        // return array type value
        $value = ['abc', 'def', 'ghi' => ['jkl', 'mno']];

        $result->value = serialize($value); 
        $result->type = 'serialized';

        $repository->get('val')->shouldBeCalled()->willReturn($result);
        $this->get('val')->shouldReturn($value);

        // return object type value
        $value = new \stdClass;
        $value->abc = ['def'];
        $value->ghi = 'jkl';

        $result->value = serialize($value);

        $repository->get('val')->shouldBeCalled()->willReturn($result);
        $this->get('val')->shouldBeLike($value);
    }

    function it_should_check_cache_first_when_retrieving(Config $repository, Cache $cache)
    {
        $key = md5('config::val');

        $value = ['abc', 'def', 'ghi' => ['jkl', 'mno']];

        $result = new \stdClass;
        $result->key = 'val';
        $result->value = serialize($value); 
        $result->type = 'serialized';

        $cache->get($key)->shouldBeCalled()->willReturn($result);
        $repository->get('val')->shouldNotBeCalled();

        $this->get('val')->shouldReturn($value);
    }

    function it_can_determine_if_config_is_exist(Config $repository)
    {
        $value = ['abc', 'def', 'ghi' => ['jkl', 'mno']];

        $result = new \stdClass;
        $result->key = 'val';
        $result->value = serialize($value); 
        $result->type = 'serialized';

        $repository->get('val')->shouldBeCalled()->willReturn($result);

        $this->has('val')->shouldReturn(true);

        $repository->get('another_val')->shouldBeCalled()->willReturn(null);

        $this->has('another_val')->shouldReturn(false);
    }

    function it_can_set_value(Config $repository, Cache $cache)
    {
        $key = md5('config::val');
        $allKey = md5('config::all');
        $value = 'value';

        // scalar type value
        $result = new \stdClass;
        $result->key = 'val';
        $result->value = $value;
        $result->type = 'unserialized';

        $repository->save('val', $value, 'unserialized')->shouldBeCalled();
        $cache->forget($allKey)->shouldBeCalled();
        $cache->put($key, $result, 10)->shouldBeCalled();

        $this->set('val', $value);

        // array type value
        $value = ['abc', 'def', 'ghi' => ['jkl']];

        $result->value = serialize($value);
        $result->type = 'serialized';

        $repository->save('val', serialize($value), 'serialized')->shouldBeCalled();
        $cache->forget($allKey)->shouldBeCalled();
        $cache->put($key, $result, 10)->shouldBeCalled();

        $this->set('val', $value);

        // object type value
        $value = new \stdClass;
        $value->abc = 'def';
        $value->ghi = ['jkl'];

        $result->value = serialize($value);

        $repository->save('val', serialize($value), 'serialized')->shouldBeCalled();
        $cache->forget($allKey)->shouldBeCalled();
        $cache->put($key, $result, 10)->shouldBeCalled();

        $this->set('val', $value);
    }

    function it_can_retrieve_all_configuration(Config $repository, Cache $cache)
    {
        $cacheKey = md5('config::all');
        $results = [];
        $expecteds = [];
        $pivots = [];

        for ($i=0; $i < 10 ; $i++) { 
            $value = new \stdClass;
            $value->abc = 'def';
            $value->ghi = ['jkl'];

            $key = 'val'.$i;
            $result = new \stdClass;
            $result->key = $key;
            $result->value = serialize($value);
            $result->type = 'serialized';

            $results[] = $result;
            $expecteds[$key] = $value;
        }

        $cache->get($cacheKey)->shouldBeCalled();
        $repository->all()->shouldBeCalled()->willReturn($results);
        $cache->put($cacheKey, $results, 1440)->shouldBeCalled();
        $this->all()->shouldBeLike($expecteds);
    }
}
