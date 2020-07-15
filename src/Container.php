<?php

declare(strict_types=1);

namespace Furious\Container;

use Furious\Container\Exception\DefinitionNotFoundException;
use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    private array $definitions = [];
    
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new DefinitionNotFoundException($id);
        }

        return $this->definitions[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->definitions);
    }

    public function put($id, $value): void
    {
        $this->definitions[$id] = $value;
    }
}