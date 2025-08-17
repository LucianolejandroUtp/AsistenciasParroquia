<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    protected $fillable = [
        'created_by',
        'date',
        'time',
        'title',
        'notes',
        'estado',
        'unique_id'
    ];
    
    // Usar timestamps personalizados
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];
    
    // Relaciones
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
