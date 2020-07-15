<?php

declare(strict_types=1);

namespace Tests\Furious\Container\Unit;

use Furious\Container\Exception\DefinitionNotFoundException;
use PHPUnit\Framework\TestCase;
use Furious\Container\Container;
use Psr\Container\ContainerExceptionInterface;
use stdClass;

class ContainerTest extends TestCase
{
    public function testPutString(): void
    {
        $container = new Container;
        $container->put($param = 'param', $value = 'value');
        $this->assertEquals($value, $container->get($param));
    }

    public function testPutInteger(): void
    {
        $container = new Container;
        $container->put($param = 'param', $value = 222);
        $this->assertEquals($value, $container->get($param));
    }

    public function testPutArray(): void
    {
        $container = new Container;
        $container->put($param = 'param', $value = [222, 1111]);
        $this->assertEquals($value, $container->get($param));
    }

    public function testPutObject(): void
    {
        $container = new Container;
        $container->put($param = 'param', $value = stdClass::class);
        $this->assertEquals($value, $container->get($param));
    }

    public function testPutClosure(): void
    {
        $container = new Container;
        $container->put($param = 'param', $value = function () {
            return 2 + 2;
        });
        $this->assertEquals(4, $container->get($param));
    }

    public function testHas(): void
    {
        $container = new Container;

        $container->put($param = 'param1', 'value1');

        $this->assertTrue($container->has($param));
        $this->assertFalse($container->has('value2'));
    }

    public function testNotFoundString(): void
    {
        $container = new Container;

        $value = 'some value';
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectExceptionMessage('Definition not found for {' . $value . '}');

        $container->get($value);
    }

    public function testNotFoundInteger(): void
    {
        $container = new Container;

        $value = 111222;
        $this->expectException(ContainerExceptionInterface::class);
        $this->expectExceptionMessage('Definition not found for {' . $value . '}');

        $container->get($value);
    }

    public function testSingleton(): void
    {
        $container = new Container;

        $container->put($param = 'param', function () {
            return new stdClass;
        });

        $first = $container->get($param);
        $second  = $container->get($param);

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertEquals($first, $second);
    }

    public function testPass(): void
    {
        $container = new Container;

        $container->put('param', $value = 11111);
        $container->put($param = 'param1', function (Container $container) {
            return $container->get('param');
        });

        $this->assertEquals($container->get('param'), $container->get('param1'));
    }

    public function testSet(): void
    {
        $container = new Container;

        $container->set('param', $value = 11111);
        $container->set($param = 'param1', function (Container $container) {
            return $container->get('param');
        });

        $this->assertEquals($container->get('param'), $container->get('param1'));
    }

    public function testAutoInstant(): void 
    {
        $container = new Container();

        $this->assertNotNull($value1 = $container->get(stdClass::class));
        $this->assertNotNull($value2 = $container->get(stdClass::class));

        $this->assertInstanceOf(stdClass::class, $value1);
        $this->assertInstanceOf(stdClass::class, $value2);

        $this->assertSame($value1, $value2);
    }

    // autowire tests

    public function testAutoWire(): void 
    {
        $container = new Container();

        $third = $container->get(Third::class);

        $this->assertNotNull($third);
        $this->assertInstanceOf(Third::class, $third);

        $this->assertNotNull($second = $third->second);
        $this->assertInstanceOf(Second::class, $second);

        $this->assertNotNull($First = $second->first);
        $this->assertInstanceOf(First::class, $First);
    }

    public function testAutowireScalar(): void
    {
        $container = new Container();

        $this->expectException(DefinitionNotFoundException::class);
        $container->get(ScalarWithOutDefault::class);
    }

    public function testAutowireScalarDefault(): void
    {
        $container = new Container();

        $scalar = $container->get(ScalarWithArrayAndDefault::class);
        $this->assertNotNull($scalar);
        $this->assertNotNull($First = $scalar->first);
        $this->assertInstanceOf(First::class, $First);
        $this->assertEquals([], $scalar->array);
        $this->assertEquals(10, $scalar->default);
    }
}

class First
{

}

class Second
{
    public First $first;

    public function __construct(First $first)
    {
        $this->first = $first;
    }
}

class Third
{
    public Second $second;

    public function __construct(Second $second)
    {
        $this->second = $second;
    }
}

class ScalarWithArrayAndDefault
{
    public First $first;
    public array $array;
    public int $default;

    public function __construct(First $first, array $array, $default = 10)
    {
        $this->first = $first;
        $this->array = $array;
        $this->default = $default;
    }
}

class ScalarWithOutDefault
{
    public First $first;
    /** @var mixed */
    public $some;

    public function __construct(First $first, $some)
    {
        $this->first = $first;
        $this->some = $some;
    }
}