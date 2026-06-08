<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('matricule', 30)->nullable();   // matricule scanné (même si élève introuvable)
            $table->boolean('found')->default(false);       // élève trouvé ?
            $table->boolean('in_order')->default(false);    // en règle au moment du scan
            $table->boolean('inscription_paid')->default(false);
            $table->boolean('month_paid')->default(false);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('classroom', 50)->nullable();    // snapshot classe
            $table->timestamp('scanned_at')->useCurrent();
            $table->timestamps();

            $table->index('scanned_at');
            $table->index(['student_id', 'scanned_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
