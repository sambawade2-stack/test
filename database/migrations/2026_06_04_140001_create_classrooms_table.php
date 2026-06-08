<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                   // ex: "6ème A", "Terminale S"
            $table->string('level', 30);                  // ex: "6ème", "5ème", "Terminale"
            $table->string('section', 10)->nullable();    // ex: "A", "B", "S", "L"
            $table->string('cycle', 20)->default('secondaire'); // "collège" | "lycée"
            $table->unsignedSmallInteger('max_students')->default(50);
            $table->foreignId('school_year_id')->constrained()->cascadeOnDelete();
            $table->string('main_teacher')->nullable();   // Professeur principal
            $table->timestamps();

            $table->unique(['level', 'section', 'school_year_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
