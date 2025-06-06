<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'tenant_id',
        'profile_img',
        'password',
        'user_uuid',
        'default_password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function loggedInAs()
    {
        return $this->belongsTo(Role::class, 'active_role_id');
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }

    public function church()
    {
        return $this->hasOne(Church::class);
    }

    // Program Director
    public function director()
    {
        return $this->hasOne(Program::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function userDocuments()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
