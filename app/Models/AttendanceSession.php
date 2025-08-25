<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AttendanceSession extends Model
{
    use HasFactory;

    /**
     * Custom timestamps to match database schema
     */
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * The table associated with the model.
     */
    protected $table = 'attendance_sessions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'created_by',
        'date',
        'time',
        'title',
        'notes',
        'estado',
        'unique_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'unique_id',
    ];

    /**
     * Get the user who created this session.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all attendances for this session.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'attendance_session_id');
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    /**
     * Scope a query to include active and closed sessions (excludes deleted).
     */
    public function scopeActiveOrClosed($query)
    {
        return $query->whereIn('estado', ['ACTIVO', 'CERRADO']);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by specific date.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    /**
     * Scope a query to filter future sessions.
     */
    public function scopeFuture($query)
    {
        return $query->where('date', '>', now()->toDateString());
    }

    /**
     * Scope a query to filter past sessions.
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString());
    }

    /**
     * Scope a query to filter today's sessions.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope a query to order by date descending.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('date', 'desc')->orderBy('time', 'desc');
    }

    /**
     * Scope a query to order by date ascending.
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('date', 'asc')->orderBy('time', 'asc');
    }

    /**
     * Get the session's display title.
     */
    public function getDisplayTitleAttribute(): string
    {
        if ($this->title) {
            return $this->title;
        }

        return 'Sesión del ' . $this->date->format('d/m/Y');
    }

    /**
     * Get the session's formatted date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        $formatted = $this->date->format('d/m/Y');
        
        if ($this->time) {
            $formatted .= ' a las ' . $this->time->format('H:i');
        }

        return $formatted;
    }

    /**
     * Check if this session is in the future.
     */
    public function isFuture(): bool
    {
        return $this->date > now()->toDateString();
    }

    /**
     * Check if this session is today.
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if this session is in the past.
     */
    public function isPast(): bool
    {
        return $this->date < now()->toDateString();
    }

    /**
     * Check if this session is closed.
     */
    public function isClosed(): bool
    {
        return $this->estado === 'CERRADO';
    }

    /**
     * Check if attendance can be taken for this session.
     */
    public function canTakeAttendance(): bool
    {
        // No permitir registro en sesiones cerradas
        if ($this->isClosed()) {
            return false;
        }

        // En desarrollo, permitir registro para cualquier sesión activa
        if (config('app.env') === 'local') {
            return $this->estado === 'ACTIVO';
        }
        
        // En producción, solo permitir para sesiones de hoy o anteriores
        return $this->date <= now()->toDateString() && $this->estado === 'ACTIVO';
    }

    /**
     * Check if this session can be deleted.
     */
    public function canBeDeleted(): bool
    {
        // Cannot delete sessions that already have attendance records
        return $this->attendances()->count() === 0;
    }

    /**
     * Check if this session can be closed.
     */
    public function canBeClosed(): bool
    {
        return $this->estado === 'ACTIVO';
    }

    /**
     * Check if this session can be reopened.
     */
    public function canBeReopened(): bool
    {
        return $this->estado === 'CERRADO';
    }

    /**
     * Get attendance statistics for this session.
     */
    public function getAttendanceStatsAttribute(): array
    {
        $attendances = $this->attendances;
        $total = $attendances->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'justified' => 0,
                'attendance_rate' => 0,
            ];
        }

        $present = $attendances->where('status', 'present')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $late = $attendances->where('status', 'late')->count();
        $justified = $attendances->where('status', 'justified')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'justified' => $justified,
            'attendance_rate' => round(($present + $late) / $total * 100, 2),
        ];
    }

    /**
     * Get session summary for reports and statistics.
     */
    public function getSessionSummary(): array
    {
        $attendances = $this->attendances;
        $totalStudents = Student::where('estado', 'ACTIVO')->count(); // Total de estudiantes del sistema
        $attendanceCount = $attendances->count();
        
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $absentCount = $totalStudents - $attendanceCount; // Los que no tienen registro se consideran ausentes
        $justifiedCount = $attendances->where('status', 'justified')->count();
        
        $attendancePercentage = $totalStudents > 0 
            ? round((($presentCount + $lateCount) / $totalStudents) * 100) 
            : 0;

        return [
            'total_students' => $totalStudents,
            'present_count' => $presentCount,
            'late_count' => $lateCount,
            'absent_count' => $absentCount,
            'justified_count' => $justifiedCount,
            'attendance_percentage' => $attendancePercentage,
        ];
    }

    /**
     * Accessor for attendance percentage
     */
    public function getAttendancePercentageAttribute()
    {
        return $this->getSessionSummary()['attendance_percentage'];
    }

    /**
     * Accessor for present count
     */
    public function getPresentCountAttribute()
    {
        return $this->getSessionSummary()['present_count'];
    }

    /**
     * Accessor for late count
     */
    public function getLateCountAttribute()
    {
        return $this->getSessionSummary()['late_count'];
    }

    /**
     * Accessor for absent count
     */
    public function getAbsentCountAttribute()
    {
        return $this->getSessionSummary()['absent_count'];
    }

    /**
     * Accessor for total students
     */
    public function getTotalStudentsAttribute()
    {
        return $this->getSessionSummary()['total_students'];
    }

    /**
     * Get students who attended this session.
     */
    public function presentStudents()
    {
        return $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->with('student');
    }

    /**
     * Get students who were absent from this session.
     */
    public function absentStudents()
    {
        return $this->attendances()
            ->whereIn('status', ['absent', 'justified'])
            ->with('student');
    }

    /**
     * Generate automatic title based on date.
     */
    public function generateAutoTitle(): string
    {
        return 'Catequesis del ' . $this->date->format('d/m/Y');
    }

    /**
     * Set automatic title if none provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($session) {
            if (empty($session->title)) {
                $session->title = 'Catequesis del ' . Carbon::parse($session->date)->format('d/m/Y');
            }
        });
    }
}
