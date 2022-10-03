<?php

namespace Src\Usuario\Gestion\Infrastructure\Persistence;

use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Domain\UsuarioRepositoryInterface;
use Src\Usuario\Gestion\Infrastructure\Web\PermisosTransformer;
use Src\Utils\Foundation\BaseEntity\BaseEntity;
use Src\Utils\Foundation\BaseRepository\BaseRepository;

class UsuarioRepository extends BaseRepository implements UsuarioRepositoryInterface
{
    /** @inheritDoc */
    protected array $relations = ['permissions', 'roles.permissions'];

    /**
     * BaseRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        parent::__construct($model, Usuario::class);
    }

    /**
     * Encuentra un elemento por el criterio de busqueda
     *
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @param array $appends Columnas 'calculadas' (accessors) en el modelo.
     *
     * @return Usuario
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Database\MultipleRecordsFoundException
     */
    public function findByCriteria(
        array $criteria,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): BaseEntity {
        $model = $this->model->select($columns)
            ->with($this->relations($relations))
            ->where($criteria)
            ->sole()
            ->append($appends);

        $modelArray = $model->toArray();

        $modelArray['permisos'] = PermisosTransformer::fromSpatie($model->getAllPermissions());

        $resultado = $this->hydrate($this->entity, $modelArray);
        // dd($resultado,$modelArray);

        return $resultado;
    }

    public function findByEmail(string $email, array $columns = ['*'], array $relations = [], array $appends = []): ?BaseEntity
    {
        return $this->findByCriteria(['email' => $email], $columns, $relations, $appends);
    }

    public function findByPhoneNumber(string $phoneNumber, array $columns = ['*'], array $relations = [], array $appends = []): ?BaseEntity
    {
        return $this->findByCriteria(['phone_number' => $phoneNumber], $columns, $relations, $appends);
    }

    public function create(BaseEntity $usuario): bool
    {
        $userModel = $this->model->create($usuario->toArray());

        $userModel->givePermissionTo($usuario->getPermisos());

        return true;
    }

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param BaseEntity $payload
     *
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateById(int $modelId, BaseEntity $payload): bool
    {
        $usuario = $payload->toArray();
        $permisos = $payload->getPermisos();
        unset($usuario['permisos']);

        if ($usuario['password'] === null) {
            unset($usuario['password']);
        }

        $user = $this->model->findOrFail($modelId);
        $user->syncPermissions($permisos);

        return $user->update($usuario);
    }
}
