<?php

namespace Src\Utils\Foundation\BaseRepository;

use Illuminate\Support\Collection;
use Src\Utils\Foundation\BaseEntity\BaseEntity;

interface BaseRepositoryInterface
{
    public function datagrid(array $datagrid);

    public function nextValId(): int;

    public function all(array $columns = ['*'], array $relations = []): Collection;

    public function findByCriteria(array $criteria, array $columns = ['*'], array $relations = [], array $appends = []): BaseEntity;

    public function getByCriteria(array $criteria, array $columns = ['*'], array $relations = [], array $appends = []): Collection;

    public function findById(int $modelId, array $columns = ['*'], array $relations = [], array $appends = []): ?BaseEntity;

    public function create(BaseEntity $entity): bool;

    public function updateByCriteria(array $criteria, array $payload): int;

    public function updateById(int $modelId, BaseEntity $payload): bool;

    public function deleteById(int $modelId): bool;
}
