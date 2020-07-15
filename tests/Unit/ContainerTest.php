<?php

declare(strict_types=1);

namespace Tests\Furious\Container\Unit;

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

    public function testAutoInstant()
    {
        $container = new Container();

        $this->assertNotNull($value1 = $container->get(stdClass::class));
        $this->assertNotNull($value2 = $container->get(stdClass::class));

        $this->assertInstanceOf(stdClass::class, $value1);
        $this->assertInstanceOf(stdClass::class, $value2);

        $this->assertSame($value1, $value2);
    }
}