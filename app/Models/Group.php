<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
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
    protected $table = 'groups';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
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
     * Get all students belonging to this group.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'group_id');
    }

    /**
     * Get only active students in this group.
     */
    public function activeStudents(): HasMany
    {
        return $this->students()->where('estado', 'ACTIVO');
    }

    /**
     * Scope a query to only include active groups.
     */
    public function scopeActive($query)
    {
        return $query->where('estado', 'ACTIVO');
    }

    /**
     * Scope a query to order by group code.
     */
    public function scopeOrderByCode($query)
    {
        return $query->orderBy('code');
    }

    /**
     * Get the total count of students in this group.
     */
    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Get the count of active students in this group.
     */
    public function getActiveStudentCountAttribute(): int
    {
        return $this->activeStudents()->count();
    }

    /**
     * Get the full display name for this group.
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    /**
     * Check if this group is Group A.
     */
    public function isGroupA(): bool
    {
        return strtoupper($this->code) === 'A';
    }

    /**
     * Check if this group is Group B.
     */
    public function isGroupB(): bool
    {
        return strtoupper($this->code) === 'B';
    }

    /**
     * Get the expected student count for this group.
     * Group A: 40 students, Group B: 38 students
     */
    public function getExpectedStudentCountAttribute(): int
    {
        return $this->isGroupA() ? 40 : 38;
    }

    /**
     * Check if the group has all expected students.
     */
    public function isComplete(): bool
    {
        return $this->active_student_count >= $this->expected_student_count;
    }

    /**
     * Get missing student count to complete the group.
     */
    public function getMissingStudentCountAttribute(): int
    {
        return max(0, $this->expected_student_count - $this->active_student_count);
    }
}
