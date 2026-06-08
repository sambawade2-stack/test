<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('employee_number', 20)->unique();
            $table->string('first_name', 60);
            $table->string('last_name', 60);
            $table->string('email', 100)->unique()->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->enum('gender', ['M', 'F']);
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 50)->default('Sénégalaise');
            $table->string('photo')->nullable();

            // Classification
            $table->enum('category', ['administratif', 'appoint']); // administratif | appoint
            $table->string('position', 80);  // ex: Secrétaire, Gardien, Technicien...
            $table->date('hire_date');
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->enum('contract_type', ['permanent', 'contractuel', 'stagiaire'])->default('permanent');
            $table->enum('status', ['active', 'inactive', 'leave'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
