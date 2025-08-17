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
     * Check if attendance can be taken for this session.
     */
    public function canTakeAttendance(): bool
    {
        // Can take attendance for today's sessions or past sessions
        // But not for future sessions (unless it's today)
        return $this->date <= now()->toDateString();
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
