<?php

namespace Tests\Integration\Usuario;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\MultipleRecordsFoundException;
use Illuminate\Database\QueryException;
use Spatie\Permission\Models\Role;
use Src\Usuario\Gestion\Domain\Usuario;
use Src\Usuario\Gestion\Infrastructure\Persistence\User;
use Src\Usuario\Gestion\Infrastructure\Persistence\UsuarioRepository;
use Src\Usuario\Gestion\Infrastructure\Web\PermisosTransformer;
use Tests\TestCase;

class UsuarioRepositoryTest extends TestCase
{
    /**
     * @param User $user
     * @return array
     */
    protected function userModelToArray(User $user): array
    {
        $usuarioEsperado = $this->hydrate(Usuario::class, $user->toArray());

        $permisos = PermisosTransformer::fromSpatie($user->getAllPermissions());
        $usuarioEsperado->changePermisos($permisos);
        $user = $user->toArray();
        unset(
            $user['updated_at'],
            $user['created_at'],
            $user['permissions'],
        );
        $user['permisos'] = $permisos;

        return $usuarioEsperado->toArray();
    }

    /** @test*/
    function devuelve_un_usuario_por_su_identificador()
    {
        User::factory(10)->create();

        $email = 'myEmail@email.com';
        $telefono = '666 666 666';
        $permisos = ['hacer cosas', 'hacer más cosas'];

        $this->crearPermisos($permisos);

        $usuarioEsperado = User::factory()->create([
            'email' => $email,
            'phone_number' => $telefono,
        ]);
        $usuarioEsperado->givePermissionTo($permisos);
        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioResultado = $repository->findByEmail($email)->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);

        $usuarioResultado = $repository->findByPhoneNumber($telefono)->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);

        $usuarioResultado = $repository->findById($usuarioEsperado['id'])->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);

        $this->assertContains($permisos[0], $usuarioResultado['permisos']);
        $this->assertContains($permisos[1], $usuarioResultado['permisos']);
    }

    public function criteriosDeBusquedaDataProvider()
    {
        return [
            'Por name' => ['name', 'Juan Valdés'],
            'Por description' => ['description', 'Una descripción'],
        ];
    }

    /**
     * @test
     * @dataProvider criteriosDeBusquedaDataProvider
     */
    function devuelve_un_usuario_dado_un_criterio_de_busqueda($key, $value)
    {
        User::factory(10)->create();

        $permisos = ['hacer cosas', 'hacer más cosas'];

        $this->crearPermisos($permisos);

        $usuarioEsperado = User::factory()->create([$key => $value]);
        $usuarioEsperado->givePermissionTo($permisos);
        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioResultado = $repository->findByCriteria([$key => $value])->toArray();

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);
        $this->assertContains($permisos[0], $usuarioResultado['permisos']);
        $this->assertContains($permisos[1], $usuarioResultado['permisos']);
    }

    /** @test*/
    public function lanza_excepcion_cuando_no_encuentra_el_usuario_por_el_criterio_de_busqueda()
    {
        User::factory(10)->create();

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByCriteria(['name' => 'Juan Valdés']);
    }

    /** @test*/
    public function lanza_excepcion_cuando_cuando_obtiene_varios_resultados_por_el_criterio_de_busqueda()
    {
        User::factory(10)->create();

        User::factory(2)->create(['name' => 'Juan Valdés']);

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(MultipleRecordsFoundException::class);
        $repository->findByCriteria(['name' => 'Juan Valdés']);
    }

    /** @test*/
    function devuelve_el_usuario_con_todos_los_permisos_directos()
    {
        User::factory(10)->create();

        $email = 'myEmail@email.com';
        $telefono = '666 666 666';
        $permisos = ['hacer cosas', 'hacer más cosas'];

        $this->crearPermisos($permisos);

        $usuarioEsperado = User::factory()->create([
            'email' => $email,
            'phone_number' => $telefono,
        ]);
        $usuarioEsperado->givePermissionTo($permisos);

        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioResultado = $repository->findByEmail($email)->toArray();

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);

        $usuarioResultado = $repository->findByPhoneNumber($telefono)->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);

        $usuarioResultado = $repository->findById($usuarioEsperado['id'])->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);
    }

    /** @test*/
    function devuelve_el_usuario_con_todos_los_permisos_dados_mediante_los_roles()
    {
        User::factory(10)->create();

        $email = 'myEmail@email.com';
        $telefono = '666 666 666';

        $usuarioEsperado = User::factory()->create([
            'email' => $email,
            'phone_number' => $telefono,
        ]);

        $nuevoRol = Role::firstOrCreate(['name' => 'nuevo rol']);
        $permisos = ['hacer cosas', 'hacer más cosas'];
        $this->crearPermisos($permisos);
        $nuevoRol->givePermissionTo($permisos);

        $usuarioEsperado->assignRole(['administrador', $nuevoRol]);
        $totalPermisosEsperados = $usuarioEsperado->getPermissionsViaRoles()->count();

        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioResultado = $repository->findByEmail($email);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());

        $usuarioResultado = $repository->findByPhoneNumber($telefono);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());

        $usuarioResultado = $repository->findById($usuarioEsperado['id']);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());
    }

    /** @test*/
    function devuelve_el_usuario_con_todos_los_permisos_directos_y_los_dados_mediante_los_roles()
    {
        User::factory(10)->create();

        $email = 'myEmail@email.com';
        $telefono = '666 666 666';

        $usuarioEsperado = User::factory()->create([
            'email' => $email,
            'phone_number' => $telefono,
        ]);

        $nuevoRol = Role::firstOrCreate(['name' => 'nuevo rol']);
        $permisos = ['hacer cosas', 'hacer más cosas', 'permiso directo'];
        $this->crearPermisos($permisos);

        $usuarioEsperado->givePermissionTo(array_pop($permisos));
        $nuevoRol->givePermissionTo($permisos);

        $usuarioEsperado->assignRole(['administrador', $nuevoRol]);
        $totalPermisosEsperados = $usuarioEsperado->getAllPermissions()->count();

        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioResultado = $repository->findByEmail($email);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());

        $usuarioResultado = $repository->findByPhoneNumber($telefono);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());

        $usuarioResultado = $repository->findById($usuarioEsperado['id']);

        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado->toArray());
        $this->assertCount($totalPermisosEsperados, $usuarioResultado->getPermisos());
    }

    /** @test*/
    function lanza_excepcion_cuando_no_encuentra_ningun_usuario_por_email()
    {
        User::factory(10)->create();

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByEmail('myEmail@email.com')->toArray();
    }

    /** @test*/
    function lanza_excepcion_cuando_no_encuentra_ningun_usuario_por_telefono()
    {
        User::factory(10)->create();

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByPhoneNumber('666 666 666')->toArray();
    }

    /** @test*/
    function lanza_excepcion_cuando_no_encuentra_ningun_usuario_por_id()
    {
        User::factory(10)->create();

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $repository->findById(-1)->toArray();
    }

    public function permisosDataProvider()
    {
        return [
            'Sin permisos' => [[]],
            'Con un único permiso' => [['permiso_inventado']],
            'Con múltiples permisos' => [['permiso inventado', 'otro_permiso_inventado']]
        ];
    }
    /**
     * @test
     * @dataProvider permisosDataProvider
     */
    function persiste_un_usuario_en_la_bdd($permisos)
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $id = $repository->nextValId();
        $user = User::factory()->make();

        $this->crearPermisos($permisos);

        $usuario = new Usuario(
            id: $id,
            name: $user->name,
            email: $user->email,
            phoneNumber: $user->phone_number,
            description: $user->description,
            avatar: $user->avatar,
            permisos: $permisos,
            password: 'password'
        );

        $this->assertTrue($repository->create($usuario));

        $usuarioEsperado = $usuario->toArray();
        unset($usuarioEsperado['permisos']);

        $this->assertDatabaseHas('users', $usuarioEsperado);

        $this->assertTrue(User::find($id)->hasAllPermissions($permisos));
    }

    /** @test*/
    function lanza_excepcion_cuando_persiste_un_usuario_con_id_repetido()
    {
        $usuarioExistente = User::factory()->create([
            'email' => 'miemail@zataca.com',
            'phone_number' => '123456789',
        ]);

        $usuario = new Usuario(
            id: $usuarioExistente->id,
            name: 'Miguel',
            email: 'otroEmail@zataca.com',
            phoneNumber: '666666666',
            description: 'Una descripción',
            avatar: '',
            permisos: [],
            password: 'password'
        );

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[23505]: Unique violation: 7 ERROR:  llave duplicada viola restricción de unicidad «users_pkey»');
        $repository->create($usuario);
    }

    /** @test*/
    function lanza_excepcion_cuando_persiste_un_usuario_con_email_repetido()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioExistente = User::factory()->create([
            'email' => 'miemail@zataca.com',
            'phone_number' => '123456789',
        ]);

        $id = $repository->nextValId();
        $usuario = new Usuario(
            id: $id,
            name: 'Miguel',
            email: $usuarioExistente->email,
            phoneNumber: '666666666',
            description: 'Una descripción',
            avatar: '',
            permisos: [],
            password: 'password'
        );


        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[23505]: Unique violation: 7 ERROR:  llave duplicada viola restricción de unicidad «users_email_unique');
        $repository->create($usuario);
    }

    /** @test*/
    function lanza_excepcion_cuando_persiste_un_usuario_con_telefono_repetido()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $usuarioExistente = User::factory()->create([
            'email' => 'miemail@zataca.com',
            'phone_number' => '123456789',
        ]);

        $id = $repository->nextValId();
        $usuario = new Usuario(
            id: $id,
            name: 'Miguel',
            email: 'otroEmail@zataca.com',
            phoneNumber: $usuarioExistente->phone_number,
            description: 'Una descripción',
            avatar: '',
            permisos: [],
            password: 'password'
        );


        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[23505]: Unique violation: 7 ERROR:  llave duplicada viola restricción de unicidad «users_phone_number_unique');
        $repository->create($usuario);
    }

    /** @test*/
    function actualiza_un_usuario_ya_existente_en_la_bdd()
    {
        $repository = $this->app->make(UsuarioRepository::class);
        $this->crearPermisos(['primer permiso', 'segundo permiso', 'permisos editados']);

        $usuario = User::factory()->create();
        $usuario->givePermissionTo(['primer permiso', 'segundo permiso']);

        $usuarioActualizado = new Usuario(
            id: $usuario->id,
            name: 'Miguel',
            email: 'cambioemail@zataca.com',
            phoneNumber: '123456789',
            description: 'nueva descripción',
            avatar: 'mi/avatar',
            permisos: [],
            password: '12345admin'
        );

        $repository->updateById($usuario->id, $usuarioActualizado);

        $datosEsperados = $usuarioActualizado->toArray();
        $datosAntiguos = $usuario->toArray();
        unset($datosEsperados['permisos'], $datosAntiguos['permissions']);

        $this->assertDatabaseHas('users', $datosEsperados);
        $this->assertDatabaseMissing('users', $datosAntiguos);
    }

    /** @test*/
    function lanza_excepcion_cuando_actualiza_el_email_con_uno_ya_existente_de_otro_usuario()
    {
        $repository = $this->app->make(UsuarioRepository::class);
        $this->crearPermisos(['primer permiso', 'segundo permiso', 'permisos editados']);

        $usuario = User::factory()->create();
        $usuario->givePermissionTo(['primer permiso', 'segundo permiso']);

        $otroUsuario = User::factory()->create();

        $usuarioActualizado = new Usuario(
            id: $usuario->id,
            name: 'Miguel',
            email: $otroUsuario->email,
            phoneNumber: '123456789',
            description: 'nueva descripción',
            avatar: 'mi/avatar',
            permisos: [],
            password: '12345admin'
        );

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[23505]: Unique violation: 7 ERROR:  llave duplicada viola restricción de unicidad «users_email_unique»');
        $repository->updateById($usuario->id, $usuarioActualizado);
    }

    /** @test*/
    function lanza_excepcion_cuando_actualiza_el_telefono_con_uno_ya_existente_de_otro_usuario()
    {
        $repository = $this->app->make(UsuarioRepository::class);
        $this->crearPermisos(['primer permiso', 'segundo permiso', 'permisos editados']);

        $usuario = User::factory()->create();
        $usuario->givePermissionTo(['primer permiso', 'segundo permiso']);

        $otroUsuario = User::factory()->create();

        $usuarioActualizado = new Usuario(
            id: $usuario->id,
            name: 'Miguel',
            email: 'cambioemail@zataca.com',
            phoneNumber: $otroUsuario->phone_number,
            description: 'nueva descripción',
            avatar: 'mi/avatar',
            permisos: [],
            password: '12345admin'
        );

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('SQLSTATE[23505]: Unique violation: 7 ERROR:  llave duplicada viola restricción de unicidad «users_phone_number_unique»');
        $repository->updateById($usuario->id, $usuarioActualizado);
    }

    /** @test*/
    function lanza_excepcion_cuando_intenta_actualizar_un_usuario_que_no_existe()
    {
        $repository = $this->app->make(UsuarioRepository::class);
        $idInexistente = $repository->nextValId();

        $usuario = User::factory()->make();

        $usuarioActualizado = new Usuario(
            id: $idInexistente,
            name: $usuario->name,
            email: 'cambioemail@zataca.com',
            phoneNumber: $usuario->phone_number,
            description: 'nueva descripción',
            avatar: 'mi/avatar',
            permisos: [],
            password: '12345admin'
        );

        $this->expectException(ModelNotFoundException::class);

        $repository->updateById($idInexistente, $usuarioActualizado);
    }

    /** @test*/
    function devuelve_el_numero_de_los_usuarios_actualizados_que_coinciden_con_el_criterio_de_busqueda()
    {
        $descriptionInicial = 'una descripción';
        $avatarInicial = 'mi/avatar.jpg';

        $avatarSinResultados = ['avatar' => 'otro/avatar.jpg'];

        $repository = $this->app->make(UsuarioRepository::class);
        $resultado = $repository->updateByCriteria($avatarSinResultados, ['description' => $descriptionInicial]);

        $this->assertEquals(0, $resultado);
        $this->assertDatabaseMissing('users', ['description' => $descriptionInicial]);

        User::factory(10)->create(['avatar' => $avatarInicial, 'description' => $descriptionInicial]);

        $descripcionNueva = 'cambio descripción';
        $resultado = $repository->updateByCriteria(['avatar' => $avatarInicial], ['description' => $descripcionNueva]);

        $this->assertEquals(10, $resultado);

        $usuariosActualizados = $repository->getByCriteria(['description' => $descripcionNueva]);
        $this->assertEquals($descripcionNueva, $usuariosActualizados->first()->getDescription());
        $this->assertEquals($descripcionNueva, $usuariosActualizados->last()->getDescription());

        $this->assertDatabaseMissing('users', ['description' => $descriptionInicial]);
    }

    /** @test*/
    function lanza_excepcion_cuando_se_actualiza_una_columna_que_no_existe()
    {
        User::factory(10)->create(['avatar' => 'mi/avatar.jpg']);

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Undefined column: 7 ERROR:  no existe la columna «campo inventado» en la relación «users»');

        $repository->updateByCriteria(['avatar' => 'mi/avatar.jpg'], ['campo inventado' => 'una descripción']);
    }

    /** @test*/
    function lanza_excepcion_cuando_se_el_criterio_de_busqueda_no_es_ninguna_columna_existente_de_la_tabla()
    {
        User::factory(10)->create(['avatar' => 'mi/avatar.jpg']);

        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('Undefined column: 7 ERROR:  no existe la columna «columna inventada»');

        $repository->updateByCriteria(['columna inventada' => 'no existe'], ['description' => 'una descripción']);
    }

    /**
     * @test
     * Hay un problema para verificar los permisos:
     * - en local el repositorio los devuelve en orden inverso.
     * - en la pipeline se devuelven en orden normal.
     * Por tanto, la verificación del usuario se ha de realizar en dos partes:
     * 1. Todos los datos del usuario excepto los permisos.
     * 2. Los permisos. Realmente el orden es irrelevante mientras el usuario tenga los permisos.
     */
    function devuelve_todos_los_usuarios_en_una_coleccion()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $users = User::factory(10)->create();

        $permisos = ['primer permiso', 'segundo permiso', 'tercer permiso'];
        $this->crearPermisos($permisos);
        $users->last()->givePermissionTo($permisos);

        $coleccion = $repository->all();

        $this->assertCount(11, $coleccion);
        $this->assertInstanceOf(Usuario::class, $coleccion->first());

        $coleccion = $repository->all(['id', 'email', 'phone_number', 'name', 'description', 'avatar']);

        $this->assertCount(11, $coleccion);
        $this->assertInstanceOf(Usuario::class, $coleccion->last());

        $ultimoUsuarioEsperado = $this->userModelToArray($users->last());

        $ultimoUsuarioResultado = $coleccion->last()->toArray();
        $permisosUsuario = $ultimoUsuarioResultado['permisos'];

        unset($ultimoUsuarioResultado['permisos'], $ultimoUsuarioEsperado['permisos']);
        unset($ultimoUsuarioResultado['password']);
        $this->assertEqualsUsuarios($ultimoUsuarioEsperado, $ultimoUsuarioResultado);
        $this->assertTrue(in_array($permisos[0], $permisosUsuario));
        $this->assertTrue(in_array($permisos[1], $permisosUsuario));
        $this->assertTrue(in_array($permisos[2], $permisosUsuario));
    }

    public function criteriosDeBusquedaUnicosDataProvider()
    {
        return [
            'Búsqueda por email' => ['email'],
            'Búsqueda por telefono' => ['phone_number'],
            'Búsqueda por id' => ['id'],
            'Búsqueda por descripcion' => ['description'],
            'Búsqueda por avatar' => ['avatar'],
        ];
    }

    /**
     * @test
     * @dataProvider criteriosDeBusquedaUnicosDataProvider
     */
    function encuentra_un_usuario_dado_un_criterio_de_busqueda_de_un_unico_campo($criterioBusqueda)
    {
        User::factory(10)->create();

        $repository = $this->app->make(UsuarioRepository::class);

        $data = [
            'email' => 'myEmail@email.com',
            'phone_number' => '666 666 666',
            'id' => $repository->nextValId(),
            'description' => 'una descripción',
            'avatar' => 'mi/avatar.jpg',
        ];

        $usuarioEsperado = User::factory()->create([
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'id' => $data['id'],
            'description' => $data['description'],
            'avatar' => $data['avatar'],
        ]);

        $permisos = ['primer permiso', 'segundo permiso', 'tercer permiso'];
        $this->crearPermisos($permisos);
        $usuarioEsperado->givePermissionTo($permisos);

        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $usuarioResultado = $repository->findByCriteria([$criterioBusqueda => $data[$criterioBusqueda]]);

        $this->assertInstanceOf(Usuario::class, $usuarioResultado);
        $usuarioResultado = $usuarioResultado->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);
    }

    public function criteriosDeBusquedaCombinadosDataProvider()
    {
        return [
            'Búsqueda por email y telefono' => ['email', 'phone_number'],
            'Búsqueda por avatar y descripcion' => ['avatar' , 'description'],
            'Búsqueda por avatar e id' => ['avatar', 'id'],

        ];
    }

    /**
     * @test
     * @dataProvider criteriosDeBusquedaCombinadosDataProvider
     */
    function encuentra_un_usuario_dado_un_criterio_de_busqueda_que_combina_mas_de_un_campo($criterio1, $criterio2)
    {
        $repository = $this->app->make(UsuarioRepository::class);
        $data = [
            'email' => 'myEmail@email.com',
            'phone_number' => '666 666 666',
            'id' => $repository->nextValId(),
            'description' => 'una descripción',
            'avatar' => 'mi/avatar.jpg',
        ];

        $usuarioEsperado = User::factory()->create([
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'id' => $data['id'],
            'description' => $data['description'],
            'avatar' => $data['avatar'],
        ]);

        User::factory(10)->create(['avatar' => $data['avatar']]);

        $permisos = ['primer permiso', 'segundo permiso', 'tercer permiso'];
        $this->crearPermisos($permisos);
        $usuarioEsperado->givePermissionTo($permisos);

        $usuarioEsperado = $this->userModelToArray($usuarioEsperado);

        $criterioBusqueda = [$criterio1 => $data[$criterio1], $criterio2 => $data[$criterio2]];

        $usuarioResultado = $repository->findByCriteria($criterioBusqueda);

        $this->assertInstanceOf(Usuario::class, $usuarioResultado);
        $usuarioResultado = $usuarioResultado->toArray();
        $this->assertEqualsUsuarios($usuarioEsperado, $usuarioResultado);
    }

    /** @test*/
    function lanza_excepcion_cuando_busca_un_usuario_y_encuentra_varios_resultados_que_coinciden_con_el_criterio_de_busqueda()
    {
        $avatar = 'mi/avatar.jpg';

        $repository = $this->app->make(UsuarioRepository::class);

        User::factory(2)->create([
            'avatar' => $avatar,
        ]);

        $this->expectException(MultipleRecordsFoundException::class);
        $repository->findByCriteria(['avatar' => $avatar]);
    }

    /** @test*/
    function lanza_excepcion_cuando_no_se_encuentran_resultados_por_los_criterios_de_busqueda()
    {
        User::factory(10)->create()->toArray();


        $repository = $this->app->make(UsuarioRepository::class);

        $this->expectException(ModelNotFoundException::class);
        $repository->findByCriteria(['avatar' => 'mi/avatar.jpg']);
    }

    /** @test*/
    function busca_multiples_usuarios_dado_un_criterio_de_busqueda()
    {
        $datos = [
            'avatar' => 'mi/avatar.jpg',
            'description' => 'una descripción cualquiera'
        ];

        $users = User::factory(10)->create($datos);

        $permisos = ['primer permiso', 'segundo permiso', 'tercer permiso'];
        $this->crearPermisos($permisos);
        $users->first()->givePermissionTo($permisos);
        $users->last()->givePermissionTo($permisos);

        $repository = $this->app->make(UsuarioRepository::class);

        $resultado = $repository->getByCriteria($datos);

        $this->assertCount(10, $resultado);

        $primerResultado = $resultado->first();
        $this->assertInstanceOf(Usuario::class, $primerResultado);
        $this->assertEquals($users->first()->id, $primerResultado->getId());
        $this->assertEquals('mi/avatar.jpg', $primerResultado->getAvatar());
        $this->assertEquals('una descripción cualquiera', $primerResultado->getDescription());
        $this->assertEquals($permisos, $primerResultado->getPermisos());

        $ultimoResultado = $resultado->last();
        $this->assertInstanceOf(Usuario::class, $ultimoResultado);
        $this->assertEquals($users->last()->id, $ultimoResultado->getId());
        $this->assertEquals('mi/avatar.jpg', $ultimoResultado->getAvatar());
        $this->assertEquals('una descripción cualquiera', $ultimoResultado->getDescription());
        $this->assertEquals($permisos, $ultimoResultado->getPermisos());
    }

    /** @test*/
    function devuelve_una_coleccion_vacia_cuando_no_encuentra_registros_por_el_criterio_de_busqueda()
    {
        $datos = [
            'avatar' => 'mi/avatar.jpg',
            'description' => 'una descripción cualquiera'
        ];

        User::factory(10)->create()->toArray();


        $repository = $this->app->make(UsuarioRepository::class);

        $resultao = $repository->getByCriteria($datos);

        $this->assertCount(0, $resultao);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $resultao);
        $this->assertTrue($resultao->isEmpty());
    }

    /** @test*/
    function devuelve_true_cuando_elimina_un_usuario_del_sistema()
    {
        $usuario = User::factory()->create();
        $permisos = ['primer permiso', 'segundo permiso', 'tercer permiso'];
        $this->crearPermisos($permisos);
        $usuario->givePermissionTo($permisos);

        $repository = $this->app->make(UsuarioRepository::class);

        $resultado = $repository->deleteById($usuario->id);

        $this->assertTrue($resultado);

        $usuarioEsperado = $this->userModelToArray($usuario);
        unset($usuarioEsperado['permisos']);

        $this->assertDatabaseMissing('users', $usuarioEsperado);
        $this->assertDatabaseMissing('model_has_permissions', ['model_id' => $usuario->id]);
    }

    /** @test*/
    function lanza_excepcion_cuando_intenta_eliminar_un_usuario_del_sistema_que_no_existe()
    {
        $repository = $this->app->make(UsuarioRepository::class);

        $idInexistente = $repository->nextValId();
        $this->expectException(ModelNotFoundException::class);
        $repository->deleteById($idInexistente);
    }

    protected function assertEqualsUsuarios(array $usuarioEsperado, array $usuarioResultado)
    {
        unset($usuarioEsperado['password'], $usuarioEsperado['permisos']);
        unset($usuarioResultado['password'], $usuarioResultado['permisos']);

        return $this->assertEquals($usuarioEsperado, $usuarioResultado);
    }
}
