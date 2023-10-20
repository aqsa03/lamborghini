<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
        'is_root',
        'is_admin',
        'is_editor',
        'is_writer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Scope a query to only exclude root users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoRoot($query)
    {
        return $query->where('is_root', '=', '0');
    }

    public function is_writer()
    {
        return $this->is_writer or $this->is_editor or $this->is_admin or $this->is_root;
    }

    public function is_editor()
    {
        return $this->is_editor or $this->is_admin or $this->is_root;
    }

    public function is_admin()
    {
        return $this->is_admin or $this->is_root;
    }

    public function is_root()
    {
        return $this->is_root;
    }
}
