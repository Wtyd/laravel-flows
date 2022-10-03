<?php

namespace Src\Utils\Foundation\BaseEntity;

use Illuminate\Support\Collection;
use Src\Utils\Foundation\BaseEntity\Exception\MinimmValuesException;

abstract class BaseEntity
{
    /**
     * Los datos que son imprescindibles para construir la entidad (pj, ['id', 'email', 'phone_number']).
     * Se debe sobreescribir para cada clase de entidad.
     */
    const MINIMUM_VALUES = [];

    abstract public function toArray(): array;

    /**
     * Transforma una coleccion de Models a una coleccion de BaseEntity.
     *
     * @param string $entity Nombre de la clase de la entidad.
     * @param Collection $modelCollection
     * @return Collection
     */
    public static function modelCollectionToEntityCollection(string $entity, Collection $modelCollection): Collection
    {
        $elements = [];
        foreach ($modelCollection as $model) {
            $elements[] = $entity::create($model->toArray());
        }
        return collect($elements);
    }

    /**
     * Factory Method que crea una entidad a partir de un array asociativo.
     * 1. Se verifica que existen los datos mínimos imprescindibles para crear la entidad (MINIMUM_VALUES).
     * 2. Se instancia la entidad.
     *
     * @param array $data Los datos serán extraídos normalmente de un modelo
     * @return BaseEntity
     */
    public static function create(array $data): BaseEntity
    {
        self::checkMinimumValues($data);
        return static::fromState($data);
    }

    /**
     * Crea una entidad a paritr de un estado concreto. Es usado por el método 'create'.
     *
     * @param array $state
     * @return BaseEntity
     */
    abstract protected static function fromState(array $state): BaseEntity;

    /**
     * Comprueba que en el array de estado existen los datos mínimos necesarios para construir la entidad. En caso contrario se lanza excepción.
     * La diferencia entre static y self es que static usa la constante definida en la clase hija y self usaría la de la padre.
     *
     * @param array $state
     * @return void
     */
    private static function checkMinimumValues(array $state): void
    {
        $datosMinimos = array_fill_keys(static::MINIMUM_VALUES, 1);
        $control = array_intersect_key($state, $datosMinimos);

        if (count($control) < count(static::MINIMUM_VALUES)) {
            throw MinimmValuesException::forState($state, get_called_class());
        }
    }
}
