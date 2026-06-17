<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\personal\CarteraModel;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\PasswordReset;
use App\Models\personal\PermisosCarteraModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoleAndPermission;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */


    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setConfirmTokenAttribute($valor)
    {
        if (!empty($valor)) {
            $this->attributes['confirm_token'] = str_random(100);
        }
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function setLdapAttribute($valor)
    {
        if (!empty($valor)) {
            if ($valor)
                $this->attributes['ldap'] = 1;
            else
                $this->attributes['ldap'] = 0;
        } else {
            $this->attributes['ldap'] = 0;
        }
    }
    /**
     * 
     * GET METHODS
     * 
     * * */
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = \Hash::make($value);
        }
    }

    public function getFirstnameAttribute($value)
    {
        return ucwords($value);
    }

    public function getEstatusidAttribute($value)
    {
        switch ($value):
            case 1:
                $status = "Activo";
                break;
            case 2:
                $status = "Inactivo";
                break;
            case 3:
                $status = "Nuevo";
        endswitch;
        return $status;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new PasswordReset($token, $this));
    }

    public function RelationPermisos()
    {

        return $this->hasMany(PermisosCarteraModel::class, 'user_id');
    }

    public function RelationToPersonal()
    {
        return $this->hasOne(personal\PersonalModel::class, 'user_id', 'id');
    }
}
