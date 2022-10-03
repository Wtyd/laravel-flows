<?php

declare(strict_types=1);

namespace Src\Utils\Foundation\BaseEntity\Exception;

use Src\Utils\Foundation\ArrayUtils;
use UnexpectedValueException;

class MinimmValuesException extends UnexpectedValueException implements BaseEntityExceptionInterface
{
    private array $state;

    private string $entity;

    public static function forState(array $state, string $entity)
    {
        $stateToString = ArrayUtils::toString($state);
        $entityMinimumValuesToString = implode(', ', $entity::MINIMUM_VALUES);

        $message = "Faltan datos para crear una entidad $entity";
        $message .= "\nLos datos mínimos son: $entityMinimumValuesToString";
        $message .= "\nLos datos actuales son: $stateToString";
        $exception = new self($message, 400);

        $exception->state = $state;

        $exception->entity = $entity;

        return $exception;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getEntity(): string
    {
        return $this->entity;
    }
}
