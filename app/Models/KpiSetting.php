<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiSetting extends Model
{
    use HasFactory;

    // Nama jadual pangkalan data
    protected $table = 'kpi_settings';

    // Primary key sebenar dalam jadual kpi_settings
    protected $primaryKey = 'setting_id';

    // Benarkan kemaskini data secara mass-assignment
    protected $guarded = [];
}