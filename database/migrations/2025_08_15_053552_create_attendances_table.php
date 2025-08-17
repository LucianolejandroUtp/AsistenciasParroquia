<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained('attendance_sessions');
            $table->foreignId('student_id')->constrained('students');
            $table->enum('status', ['present', 'absent', 'late', 'justified']);
            $table->text('notes')->nullable(); // observaciones opcionales
            
            
            $table->enum('estado', ['ACTIVO', 'INACTIVO','ELIMINADO'])->default('ACTIVO')->nullable();
            $table->uuid('unique_id')->unique()->default(DB::raw('uuid()'))->nullable();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();
            // $table->timestamps();
            
            // Evitar duplicados: un estudiante por sesión
            $table->unique(['attendance_session_id', 'student_id']);
            $table->index('status'); // búsqueda por estado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
