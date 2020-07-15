<?php

declare(strict_types=1);

namespace Furious\Container\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class DefinitionNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public function __construct($id, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if ('' === $message) {
            $this->message = 'Definition not found for {' . (string) $id . '}';
        }
    }
}