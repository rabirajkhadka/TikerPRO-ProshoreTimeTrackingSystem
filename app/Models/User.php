<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;


class User extends Model

{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     * Get the role that belongs to the user
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }
     /**
         * Get the number of active admin users.
         */
        /**
         * Undocumented function
         *
         * @return integer
         */
        public function getActiveAdminsCount(): int
        {
            return $this->whereHas('roles', function ($query) {
                $query->where('role', 'admin')->where('activeStatus', true);
            })->count();
        }
    

    /*
     * Get the project that belongs to the user
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'user_projects', 'user_id', 'project_id');
    }

    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'user_id');
    }

    public function setPasswordAttribute($password)
    {
        if (trim($password) === '') return;
        $this->attributes['password'] =  Hash::make($password);
    }


    /**
     *
     * @param Builder $query
     * @param string $email
     * @return void
     */
    public function scopeGetByEmail(Builder $query, string $email)
    {
        return $query->where('email', $email);
    }
}
