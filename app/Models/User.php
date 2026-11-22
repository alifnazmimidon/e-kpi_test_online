<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // 1. Sambungkan ke jadual 'users'
    protected $table = 'users';

    // 2. Tetapkan Primary Key jenis string ('ILM1004')
    // 2. Tetapkan Primary Key jenis string ('ILM1004')
    protected $primaryKey = 'worker_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    public $rememberTokenName = false;

    // 3. Kolum yang boleh diisi
    protected $fillable = [
    'worker_id',
    'full_name',
    'email',
    'department_id',
    'designation',
    'phone',
    'positions',
    'profile_photo',
];

    protected $hidden = [
    'password_hash',
    'remember_token',
];

    

    // 5. Beritahu Laravel kolum password bernama 'password_hash'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // 6. Hubungan dengan jadual departments
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}