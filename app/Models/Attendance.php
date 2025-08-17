<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'notes',
        'recorded_by',
        'estado',
        'unique_id'
    ];
    
    // Usar timestamps personalizados
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    
    // Relaciones
    public function session()
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }
    
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
