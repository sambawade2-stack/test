<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 30)->unique(); // N° reçu auto-généré

            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('school_year_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained()->restrictOnDelete();

            $table->enum('payment_type', ['inscription', 'mensualite']);
            $table->unsignedTinyInteger('month')->nullable(); // 1-12 pour mensualité
            $table->unsignedSmallInteger('year')->nullable(); // ex: 2024

            $table->decimal('amount_due', 10, 2);       // Montant attendu
            $table->decimal('amount_paid', 10, 2);      // Montant payé
            $table->decimal('balance', 10, 2)->default(0); // Solde restant

            $table->enum('status', ['paid', 'partial', 'unpaid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'virement', 'mobile_money', 'cheque'])->default('cash');
            $table->date('payment_date');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            // Unicité mensualité par élève/mois/année
            $table->unique(['student_id', 'payment_type', 'month', 'year', 'school_year_id'], 'unique_student_payment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
