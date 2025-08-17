<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'description',
        'estado',
        'unique_id'
    ];
    
    // Usar timestamps personalizados
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    // Relaciones
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
