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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('groups');
            $table->string('names');
            $table->string('paternal_surname'); // apellido paterno
            $table->string('maternal_surname')->nullable(); // apellido materno (opcional)
            $table->integer('order_number'); // número de orden en la lista
            $table->string('student_code')->nullable()->unique(); // A-01, B-05, etc
            // $table->json('metadata')->nullable(); // info adicional
            
            
            $table->enum('estado', ['ACTIVO', 'INACTIVO','ELIMINADO'])->default('ACTIVO')->nullable();
            $table->uuid('unique_id')->unique()->default(DB::raw('uuid()'))->nullable();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->nullable();
            // $table->timestamps();

            
            // Index compuesto para evitar duplicados en la misma lista
            $table->unique(['group_id', 'order_number']);
            $table->index('paternal_surname'); // búsqueda por apellido paterno
            $table->index(['paternal_surname', 'maternal_surname']); // búsqueda por apellidos completos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
