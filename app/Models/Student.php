<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
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
    protected $table = 'students';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'group_id',
        'names',
        'paternal_surname',
        'maternal_surname',
        'order_number',
        'student_code',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'estado' => 'string',
        'order_number' => 'integer',
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
     * Get the group that this student belongs to.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Get all attendances for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Scope a query to only include active students.
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    /**
     * Scope a query to filter by group.
     */
    public function scopeInGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope a query to order by order number.
     */
    public function scopeOrderByNumber($query)
    {
        return $query->orderBy('order_number');
    }

    /**
     * Scope a query to search by name or surname.
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('names', 'like', "%{$searchTerm}%")
              ->orWhere('paternal_surname', 'like', "%{$searchTerm}%")
              ->orWhere('maternal_surname', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Scope a query to find by QR code.
     */
    public function scopeByQrCode($query, $qrCode)
    {
        return $query->where('student_code', $qrCode);
    }

    /**
     * Get the student's full name.
     */
    public function getFullNameAttribute(): string
    {
        $fullName = trim($this->names . ' ' . $this->paternal_surname);
        if ($this->maternal_surname) {
            $fullName .= ' ' . $this->maternal_surname;
        }
        return $fullName;
    }

    /**
     * Get the student's surnames only.
     */
    public function getSurnamesAttribute(): string
    {
        $surnames = trim($this->paternal_surname);
        if ($this->maternal_surname) {
            $surnames .= ' ' . $this->maternal_surname;
        }
        return $surnames;
    }

    /**
     * Get the student's display name with order number.
     */
    public function getDisplayNameAttribute(): string
    {
        return sprintf('%02d. %s', $this->order_number, $this->full_name);
    }

    /**
     * Get the QR code for this student (accessor for student_code).
     */
    public function getQrCodeAttribute(): string
    {
        return $this->student_code ?? '';
    }

    /**
     * Generate QR code for this student.
     * Format: {GROUP}-{FIRSTNAME}-{PATERNAL_SYLLABLE}-{MATERNAL_SYLLABLE}
     */
    public function generateQrCode(): string
    {
        $groupCode = $this->group->code ?? 'X';
        $firstName = $this->extractFirstName();
        $paternalSyllable = $this->extractFirstSyllable($this->paternal_surname);
        $maternalSyllable = $this->extractFirstSyllable($this->maternal_surname ?? '');

        return strtoupper("{$groupCode}-{$firstName}-{$paternalSyllable}-{$maternalSyllable}");
    }

    /**
     * Extract first name from names field.
     */
    private function extractFirstName(): string
    {
        $names = explode(' ', trim($this->names));
        return $this->normalizeText($names[0] ?? '');
    }

    /**
     * Extract first syllable from a surname.
     */
    private function extractFirstSyllable(string $surname): string
    {
        if (empty($surname)) {
            return '';
        }

        // Handle compound surnames (take first part)
        $surname = explode(' ', trim($surname))[0];
        $surname = $this->normalizeText($surname);

        // Extract syllable using vowel-based detection
        $vowels = ['A', 'E', 'I', 'O', 'U'];
        $syllable = '';
        $foundVowel = false;

        for ($i = 0; $i < mb_strlen($surname, 'UTF-8'); $i++) {
            $char = mb_substr($surname, $i, 1, 'UTF-8');
            $syllable .= $char;

            if (in_array(strtoupper($char), $vowels)) {
                $foundVowel = true;
                // Include consonants after the vowel until next vowel or end
                for ($j = $i + 1; $j < mb_strlen($surname, 'UTF-8'); $j++) {
                    $nextChar = mb_substr($surname, $j, 1, 'UTF-8');
                    if (in_array(strtoupper($nextChar), $vowels)) {
                        break;
                    }
                    $syllable .= $nextChar;
                }
                break;
            }
        }

        return $foundVowel ? strtoupper($syllable) : strtoupper(mb_substr($surname, 0, 3, 'UTF-8'));
    }

    /**
     * Normalize text by removing accents and handling special characters.
     */
    private function normalizeText(string $text): string
    {
        $text = trim($text);
        
        // Handle accented characters
        $accents = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ü' => 'u', 'Ü' => 'U'
        ];

        foreach ($accents as $accent => $replacement) {
            $text = str_replace($accent, $replacement, $text);
        }

        // Preserve ñ as it's important in Spanish names
        return $text;
    }

    /**
     * Get attendance statistics for this student.
     */
    public function getAttendanceStatsAttribute(): array
    {
        $total = $this->attendances()->count();
        $present = $this->attendances()->where('status', 'present')->count();
        $late = $this->attendances()->where('status', 'late')->count();
        $absent = $this->attendances()->where('status', 'absent')->count();
        $justified = $this->attendances()->where('status', 'justified')->count();

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'justified' => $justified,
            'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 2) : 0,
        ];
    }

    /**
     * Check if student was present in a specific session.
     */
    public function wasPresentInSession(int $sessionId): bool
    {
        return $this->attendances()
            ->where('attendance_session_id', $sessionId)
            ->whereIn('status', ['present', 'late'])
            ->exists();
    }

    /**
     * Get the latest attendance record.
     */
    public function getLatestAttendanceAttribute()
    {
        return $this->attendances()
            ->with('attendanceSession')
            ->latest('created_at')
            ->first();
    }
}
