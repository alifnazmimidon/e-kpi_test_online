<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiSetting extends Model
{
    use HasFactory;

    protected $table = 'kpi_settings';
    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'tasks_weightage',
        'attendance_weightage',
        'leader_weightage',
        'gold_threshold',
        'silver_threshold',
        'bronze_threshold',
    ];
}