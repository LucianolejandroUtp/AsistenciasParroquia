<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'group_id',
        'order_number',
        'first_name',
        'paternal_surname',
        'maternal_surname',
        'qr_code',
        'notes',
        'estado',
        'unique_id'
    ];
    
    // Usar timestamps personalizados
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    // Relaciones
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->paternal_surname . ' ' . $this->maternal_surname);
    }
}
