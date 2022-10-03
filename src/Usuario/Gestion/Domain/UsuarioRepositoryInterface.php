<?php

namespace Src\Usuario\Gestion\Domain;

use Src\Utils\Foundation\BaseEntity\BaseEntity;
use Src\Utils\Foundation\BaseRepository\BaseRepositoryInterface;

interface UsuarioRepositoryInterface extends BaseRepositoryInterface
{
    public function findByEmail(string $email, array $columns = ['*'], array $relations = [], array $appends = []): ?BaseEntity;

    public function findByPhoneNumber(string $phoneNumber, array $columns = ['*'], array $relations = [], array $appends = []): ?BaseEntity;
}
