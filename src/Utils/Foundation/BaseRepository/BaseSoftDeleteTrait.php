<?php

namespace Src\Utils\Foundation\BaseRepository;

use Illuminate\Support\Collection;
use Src\Utils\Foundation\BaseEntity\BaseEntity;

/**
 * Repositorio base con los principales métodos.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
trait BaseSoftDeleteTrait
{
    /**
     * Get all trashed models.
     *
     * @return Collection
     */
    public function allTrashed(): Collection
    {
        return $this->entity::modelCollectionToEntityCollection($this->entity, $this->model->onlyTrashed()->get());
    }

    /**
     * Find trashed model by id.
     *
     * @param int $modelId
     * @return BaseEntity
     */
    public function findTrashedById(int $modelId): ?BaseEntity
    {
        return $this->entity::fromState($this->model->withTrashed()->findOrFail($modelId)->toArray());
    }

    /**
     * Find only trashed model by id.
     *
     * @param int $modelId
     * @return BaseEntity
     */
    public function findOnlyTrashedById(int $modelId): ?BaseEntity
    {
        return $this->entity::fromState($this->entity, $this->model->onlyTrashed()->findOrFail($modelId)->toArray());
    }

    /**
     * Restore model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function restoreById(int $modelId): bool
    {
        return $this->findOnlyTrashedById($modelId)->restore();
    }

    /**
     * Permanently delete model by id.
     *
     * @param int $modelId
     * @return bool
     */
    public function permanentlyDeleteById(int $modelId): bool
    {
        return $this->findTrashedById($modelId)->forceDelete();
    }
}
