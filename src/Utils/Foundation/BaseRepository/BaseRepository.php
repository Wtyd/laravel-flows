<?php

namespace Src\Utils\Foundation\BaseRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Src\Usuario\Gestion\Infrastructure\Web\PermisosTransformer;
use Src\Utils\Foundation\BaseEntity\BaseEntity;
use Zataca\Hydrator\HydratorTrait;

/**
 * Repositorio base con los principales métodos.
 *
 * Relaciones del Model.
 * El BaseRepository, por defecto devolvera el modelo con todas sus relaciones ($relations) establecidas en el repositorio concreto.
 * Evitaremos usar el atributo $with en el modelo de Eloquent (https://laravel.com/docs/8.x/eloquent-relationships#eager-loading-by-default) ya
 * que:
 *      1. Pueden haber varios repositorios concretos para un mismo modelo con distintas necesidades.
 *      2. El modelo de Eloquent será siempre único.
 * De esta forma podemos hacer que cada repositorio concreto que comparte un modelo pueda devolver distintas relaciones según las necesidades del
 * módulo.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * //TODO faltan los metodos updateOrcreate y upsert
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    use HydratorTrait;

    /**
     * Relaciones del modelo que se pedirán siempre.
     *
     * @var array<string>
     */
    protected array $relations = [];

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     * @param string $entity
     */
    public function __construct(
        protected Model $model,
        protected string $entity = ''
    ) {
    }

    /**
     * Devuele un Query/Builder sobre la tabla base con el nombre de las columnas en formato para el datagrid. Esto es
     * nombreTabla.nombreColumna.
     *
     * @param array $datagrid
     * @return \Illuminate\Database\Query\Builder
     */
    public function datagrid(array $datagrid = []): \Illuminate\Database\Query\Builder
    {
        // TODO no tengo claro que este metodo vaya en el repo. Quizas el datagrid pida un Query\Builder con la tabla y el select se haga en el datagrid
        $tableName = $this->model->getTable();
        $columnas = array_map(function ($value) use ($tableName) {
            return "$tableName.$value as $tableName.$value";
        }, $datagrid);

        return DB::table($tableName)->select($columnas);
    }

    /**
     * Devuelve el siguiente valor de la secuencia.
     * El formato por defecto es: nombreTabla_columnaId_seq
     *
     * @return integer
     */
    public function nextValId(): int
    {
        $seq = $this->model->getTable() . '_id_seq';
        $query = DB::select("select nextval('$seq'::regclass)");
        return $query[0]->nextval;
    }

    /**
     * @param array $columns Si seleccionamos columnas, deben ser al menos, las mínimas imprescindibles para construir la entidad.
     * @param array $relations
     *
     * @return Collection
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        return $this->entity::modelCollectionToEntityCollection(
            $this->entity,
            $this->model->with($this->relations($relations))->get($columns)
        );
    }

    /**
     * Encuentra un elemento por el criterio de busqueda
     *
     * @
     * @param array $criteria
     * @param array $columns
     * @param array $relations
     * @param array $appends Columnas 'calculadas' (accessors) en el modelo.
     *
     * @return BaseEntity
     *
     * @throws ModelNotFoundException
     * @throws MultipleRecordsFoundException
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

        $modelArray['permisos'] = $model->permissions()->pluck('name')->toArray();

        $resultado = $this->hydrate($this->entity, $modelArray);

        return $resultado;
    }

    /**
     * Encuentra una colección de elementos por el criterio de búsqueda
    *
    * @param array $criteria
    * @param array $columns
    * @param array $relations
    * @param array $appends
    *
    * @return Collection
    */
    public function getByCriteria(
        array $criteria,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): Collection {
        $relations = !empty($relations) ? $relations : $this->relations;

        $modelCollection = $this->model->select($columns)
            ->with($this->relations($relations))
            ->where($criteria)
            ->get()
            ->append($appends);
        return $this->entity::modelCollectionToEntityCollection($this->entity, $modelCollection);
    }

    /**
     * Find model by id.
     *
     * @param int $modelId
     * @param array $columns
     * @param array $relations
     * @param array $appends
     *
     * @return BaseEntity
     *
     * @throws ModelNotFoundException
     */
    public function findById(
        int $modelId,
        array $columns = ['*'],
        array $relations = [],
        array $appends = []
    ): ?BaseEntity {
        $user = $this->findByCriteria(['id' => $modelId], $columns, $relations, $appends);
        return $user;
    }

    /**
     * Create a model.
     *
     * @param BaseEntity $entity
     *
     * @return bool
     */
    public function create(BaseEntity $entity): bool
    {
        $this->model->create($entity->toArray());

        return true;
    }

    /**
     * Updates masivos
     *
     * @param array $criteria
     * @param array $payload
     *
     * @return int El número de registros actualizados.
     *
     * @throws QueryException Cuando se intenta actualizar una columna que no existe en la tabla.
     */
    public function updateByCriteria(array $criteria, array $payload): int
    {
        return $this->model->where($criteria)->update($payload);
    }

    /**
     * Update existing model.
     *
     * @param int $modelId
     * @param BaseEntity $payload
     *
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function updateById(int $modelId, BaseEntity $payload): bool
    {
        // El QueryBuilder retorna 1 en caso de update y 0 en caso de no encontrar ningún resultado.
        // mientras que Eloquent $model->update($payload) retorna true o false.
        // Es posible que en alguna implementación concreta se use Eloquent (User)
        return boolval($this->model->where('id', $modelId)->update($payload->toArray()));
    }

    /**
     * Delete model by id.
     *
     * @param int $modelId
     *
     * @return bool
     *
     * @throws ModelNotFoundException
     */
    public function deleteById(int $modelId): bool
    {
        return $this->model->findOrFail($modelId)->delete();
    }

    /**
     * Sobreescribe las relaciones del modelo o devuelve las relaciones por defecto.
     *
     * @param array $relations
     * @return array
     */
    protected function relations(array $relations): array
    {
        return !empty($relations) ? $relations : $this->relations;
    }
}
