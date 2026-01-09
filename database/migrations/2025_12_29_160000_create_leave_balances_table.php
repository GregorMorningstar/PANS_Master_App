<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('year')->index();
            // typ urlopu (np. 'vacation', 'sick')
            $table->string('leave_type')->default('vacation')->index();
            $table->string('barcode')->unique()->nullable();

            // saldo i pola pomocnicze
            $table->integer('entitlement_days')->default(0);
            $table->integer('used_days')->default(0);
            $table->integer('remaining_days')->default(0);
            $table->integer('request_days')->default(0);
            $table->integer('request_used')->default(0);
            $table->decimal('seniority_years', 8, 4)->default(0);
            $table->decimal('work_time', 8, 2)->default(0);
            $table->decimal('education_time', 8, 2)->default(0);
            $table->date('employment_start_date')->nullable();

            // unikalność dla user + rok + typ urlopu
            $table->unique(['user_id', 'year', 'leave_type']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
