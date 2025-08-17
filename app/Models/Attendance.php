<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
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
    protected $table = 'attendances';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'attendance_session_id',
        'student_id',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
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
     * Valid attendance status values.
     */
    public const STATUS_PRESENT = 'present';
    public const STATUS_ABSENT = 'absent';
    public const STATUS_LATE = 'late';
    public const STATUS_JUSTIFIED = 'justified';

    /**
     * Get all valid status values.
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PRESENT,
            self::STATUS_ABSENT,
            self::STATUS_LATE,
            self::STATUS_JUSTIFIED,
        ];
    }

    /**
     * Get the attendance session this record belongs to.
     */
    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(AttendanceSession::class, 'attendance_session_id');
    }

    /**
     * Get the student this attendance record belongs to.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Scope a query to only include active attendance records.
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    /**
     * Scope a query to filter by attendance status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter present students (present or late).
     */
    public function scopePresent($query)
    {
        return $query->whereIn('status', [self::STATUS_PRESENT, self::STATUS_LATE]);
    }

    /**
     * Scope a query to filter absent students (absent or justified).
     */
    public function scopeAbsent($query)
    {
        return $query->whereIn('status', [self::STATUS_ABSENT, self::STATUS_JUSTIFIED]);
    }

    /**
     * Scope a query to filter by session.
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('attendance_session_id', $sessionId);
    }

    /**
     * Scope a query to filter by student.
     */
    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope a query to order by student order number.
     */
    public function scopeOrderByStudent($query)
    {
        return $query->join('students', 'attendances.student_id', '=', 'students.id')
            ->orderBy('students.order_number');
    }

    /**
     * Get the status display name in Spanish.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'Presente',
            self::STATUS_ABSENT => 'Ausente',
            self::STATUS_LATE => 'Tardanza',
            self::STATUS_JUSTIFIED => 'Justificado',
            default => 'Desconocido'
        };
    }

    /**
     * Get the status CSS class for styling.
     */
    public function getStatusCssClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'badge-success',
            self::STATUS_ABSENT => 'badge-danger',
            self::STATUS_LATE => 'badge-warning',
            self::STATUS_JUSTIFIED => 'badge-info',
            default => 'badge-secondary'
        };
    }

    /**
     * Get the status icon for display.
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PRESENT => 'fe fe-check-circle',
            self::STATUS_ABSENT => 'fe fe-x-circle',
            self::STATUS_LATE => 'fe fe-clock',
            self::STATUS_JUSTIFIED => 'fe fe-info',
            default => 'fe fe-help-circle'
        };
    }

    /**
     * Check if the student was present (present or late).
     */
    public function isPresent(): bool
    {
        return in_array($this->status, [self::STATUS_PRESENT, self::STATUS_LATE]);
    }

    /**
     * Check if the student was absent (absent or justified).
     */
    public function isAbsent(): bool
    {
        return in_array($this->status, [self::STATUS_ABSENT, self::STATUS_JUSTIFIED]);
    }

    /**
     * Check if the attendance is justified.
     */
    public function isJustified(): bool
    {
        return $this->status === self::STATUS_JUSTIFIED;
    }

    /**
     * Check if the student was late.
     */
    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    /**
     * Get formatted notes for display.
     */
    public function getFormattedNotesAttribute(): string
    {
        return $this->notes ? nl2br(e($this->notes)) : '';
    }

    /**
     * Set status with validation.
     */
    public function setStatusAttribute($value)
    {
        if (!in_array($value, self::getValidStatuses())) {
            throw new \InvalidArgumentException("Invalid attendance status: {$value}");
        }

        $this->attributes['status'] = $value;
    }

    /**
     * Create or update attendance record for a student in a session.
     */
    public static function markAttendance(int $sessionId, int $studentId, string $status, ?string $notes = null): self
    {
        return self::updateOrCreate(
            [
                'attendance_session_id' => $sessionId,
                'student_id' => $studentId,
            ],
            [
                'status' => $status,
                'notes' => $notes,
            ]
        );
    }

    /**
     * Get attendance summary for a specific session.
     */
    public static function getSessionSummary(int $sessionId): array
    {
        $attendances = self::forSession($sessionId)->get();
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

        $present = $attendances->where('status', self::STATUS_PRESENT)->count();
        $absent = $attendances->where('status', self::STATUS_ABSENT)->count();
        $late = $attendances->where('status', self::STATUS_LATE)->count();
        $justified = $attendances->where('status', self::STATUS_JUSTIFIED)->count();

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'justified' => $justified,
            'attendance_rate' => round(($present + $late) / $total * 100, 2),
        ];
    }
}
