<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number', 30)->unique();

            // Polymorphe : Teacher ou Staff
            $table->morphs('payable');

            $table->unsignedTinyInteger('month');         // 1-12
            $table->unsignedSmallInteger('year');

            $table->decimal('base_salary', 10, 2);
            $table->decimal('bonuses', 10, 2)->default(0);
            $table->decimal('deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);         // base + bonuses - deductions

            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->enum('payment_method', ['cash', 'virement', 'mobile_money', 'cheque'])->default('virement');
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['payable_id', 'payable_type', 'month', 'year'], 'unique_payroll');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
