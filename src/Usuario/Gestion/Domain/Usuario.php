<?php

declare(strict_types=1);

namespace Src\Usuario\Gestion\Domain;

use Src\Utils\Foundation\BaseEntity\BaseEntity;

class Usuario extends BaseEntity
{
    const MINIMUM_VALUES = ['id', 'email', 'phone_number'];

    protected UsuarioId $usuarioId;
    protected Password $password;

    public function __construct(
        int $id,
        string $phoneNumber,
        string $email,
        protected string $name = '',
        string $password = '',
        protected ?string $description = '',
        protected string $avatar = '',
        protected array $permisos = []
    ) {
        $this->usuarioId = new UsuarioId(id: $id, email: $email, phoneNumber: $phoneNumber);
        $this->password = new Password(value: $password);
        $this->changePermisos($permisos);
    }

    protected static function fromState(array $state): BaseEntity
    {
        return new self(
            id: $state['id'],
            name: $state['name'] ?? '',
            phoneNumber: $state['phone_number'],
            email: $state['email'],
            description: $state['description'] ?? '',
            avatar: $state['avatar'] ?? '',
            permisos: $state['permissions'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->usuarioId->getId(),
            'name' => $this->name,
            'email' => $this->usuarioId->getEmail(),
            'phone_number' => $this->usuarioId->getPhoneNumber(),
            'description' => $this->description,
            'avatar' => $this->avatar,
            'password' => $this->password->getValue(),
            'permisos' => $this->permisos
        ];
    }

    public function changeName(string $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(string $email): void
    {
        $this->usuarioId = new UsuarioId($this->usuarioId->getId(), $email, $this->usuarioId->getPhoneNumber());
    }

    public function changePhoneNumber(string $phoneNumber): void
    {
        $this->usuarioId = new UsuarioId($this->usuarioId->getId(), $this->usuarioId->getEmail(), $phoneNumber);
    }

    public function changePassword(string $password): bool
    {
        if (!$this->password->isEqualsToString($password)) {
            $this->password = new Password(value: $password);
            return true;
        }
        return false;
    }

    public function checkPassword(string $password): bool
    {
        return $this->password->isEqualsToString($password);
    }

    // TODO está acoplado a la bdd. Quizas la logica de spatie deba ir fuera d este método
    public function changePermisos(array $permisos): void
    {
        $lista = [];
        foreach ($permisos as $permiso) {
            if (is_array($permiso)) {
                $lista[] = $permiso['name'];
            } else {
                $lista[] = $permiso;
            }
        }
        $this->permisos = $lista;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->usuarioId->getEmail();
    }

    public function getPhoneNumber(): string
    {
        return $this->usuarioId->getPhoneNumber();
    }

    public function getPermisos(): array
    {
        return $this->permisos;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->usuarioId->getId();
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function equals(Usuario $usuario)
    {
        return $this->usuarioId->equals($usuario->getId());
    }
}
