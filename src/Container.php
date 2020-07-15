<?php

declare(strict_types=1);

namespace Furious\Container;

use Closure;
use Furious\Container\Exception\DefinitionNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use function array_key_exists;
use function class_exists;

final class Container implements ContainerInterface
{
    private array $values = [];
    private array $definitions;

    /**
     * Container constructor.
     * @param array $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }

    public function get($id)
    {
        if ($this->hasValue($id)) {
            return $this->values[$id];
        }

        if (!$this->hasDefinition($id)) {
            if ($this->classExists($id)) {
                return $this->values[$id] = $this->autowire($id);
            }
            throw new DefinitionNotFoundException($id);
        }

        $def = $this->definitions[$id];
        if ($def instanceof Closure) {
            return $this->values[$id] = $def($this);
        }

        return $this->values[$id] = $def;
    }

    public function set($id, $value): void
    {
        $this->put($id, $value);
    }

    public function put($id, $value): void
    {
        if ($this->hasValue($id)) {
            $this->remove($id);
        }
        $this->definitions[$id] = $value;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->definitions) or $this->classExists($id);
    }

    private function remove($id): void
    {
        unset($this->values[$id]);
    }

    private function hasDefinition($id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    private function hasValue($id): bool
    {
        return array_key_exists($id, $this->values);
    }

    private function classExists($name): bool
    {
        return class_exists((string) $name);
    }

    /**
     * @param $id
     * @return object
     * @throws ReflectionException
     */
    private function autowire($id): object
    {
        $reflection = new ReflectionClass($id);
        $arguments = [];

        if (null !== ($constructor = $reflection->getConstructor())) {
            foreach ($constructor->getParameters() as $param) {
                $arguments[] = $this->getArgumentByParameter($param);
            }
        }

        return $reflection->newInstanceArgs($arguments);
    }

    /**
     * @param ReflectionParameter $param
     * @return array|mixed
     * @throws ReflectionException
     */
    private function getArgumentByParameter(ReflectionParameter $param)
    {
        if ($paramClass = $param->getClass()) {
            return $this->get($paramClass->getName());
        } elseif ($param->isArray()) {
            return [];
        } else {
            if (!$param->isDefaultValueAvailable()) {
                throw new DefinitionNotFoundException($param->getName());
            }
            return $param->getDefaultValue();
        }
    }
}