<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'tasks_etracker';
    protected $primaryKey = 'task_id';

    protected $fillable = [
        'worker_id',
        'project_name',
        'assign_date',
        'completion_date',
        'status',
    ];
}