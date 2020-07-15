<?php

declare(strict_types=1);

namespace Furious\Container;

use Closure;
use Furious\Container\Exception\DefinitionNotFoundException;
use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    private array $definitions = [];
    private array $values = [];
    
    public function get($id)
    {
        if ($this->has($id)) {
            return $this->values[$id];
        }

        if (!$this->hasDefinition($id)) {
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
        if ($this->has($id)) {
            $this->remove($id);
        }
        $this->definitions[$id] = $value;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->values);
    }

    private function hasDefinition($id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    private function remove($id): void
    {
        unset($this->values[$id]);
    }
}