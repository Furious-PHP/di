<?php

declare(strict_types=1);

namespace Furious\Container;

use Closure;
use Furious\Container\Exception\DefinitionNotFoundException;
use Psr\Container\ContainerInterface;
use function array_key_exists;
use function class_exists;

final class Container implements ContainerInterface
{
    private array $definitions = [];
    private array $values = [];
    
    public function get($id)
    {
        if ($this->hasValue($id)) {
            return $this->values[$id];
        }

        if (!$this->hasDefinition($id)) {
            if ($this->classExists($id)) {
                return $this->values[$id] = new $id;
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
}