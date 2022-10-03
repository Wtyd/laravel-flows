<?php

namespace Src\Usuario\Gestion\Infrastructure\Persistence;

use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use App\OAuthProvider;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Src\Usuario\Gestion\Infrastructure\Persistence\UserFactory;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use HasFactory;
    use HasApiTokens;

    protected $guard_name = 'api';


    /**
     * El campo id debe ser fillable
     *
     * @var array<string>|bool
     */
    protected $guarded =  [
        'updated_at', 'created_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }


    // Necesario para el login actual
    public function withFullData()
    {
        $this->getAllPermissions();

        return $this;
    }


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
