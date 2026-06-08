<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number', 20)->unique(); // Matricule
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->date('date_of_birth');
            $table->string('place_of_birth', 80)->nullable();
            $table->enum('gender', ['M', 'F']);
            $table->string('photo')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('nationality', 50)->default('Sénégalaise');

            // Infos tuteur/parent
            $table->string('parent_name', 100)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->string('parent_email', 100)->nullable();
            $table->string('parent_relation', 30)->nullable(); // père, mère, tuteur

            // Scolarité
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();
            $table->foreignId('school_year_id')->constrained()->restrictOnDelete();
            $table->date('enrolled_at');
            $table->boolean('is_active')->default(true);
            $table->string('previous_school', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
