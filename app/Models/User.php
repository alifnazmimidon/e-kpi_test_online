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
    protected $primaryKey = 'worker_id';
    public $incrementing = false;
    protected $keyType = 'string';

    // 3. Kolum yang boleh diisi
    protected $fillable = [
        'worker_id',
        'full_name',
        'email',
        'password_hash',
        'department_id',
        'role',
        'designation',
    ];

    // 4. Sembunyikan hash password daripada output
    protected $hidden = [
        'password_hash',
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