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
            $table->string('barcode')->unique()->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('year');                 // rok kalendarzowy
            $table->enum('leave_type', ['vacation','sick','personal'])->default('vacation');
            $table->integer('entitlement_days');    // np. 20/24
            $table->integer('used_days')->default(0);
            $table->integer('remaining_days')->default(0);
            $table->integer('request_days')->default(2); // dni na żądanie
            $table->integer('request_used')->default(0);
            $table->integer('seniority_years')->default(0);   // lata stażu pracy
            $table->integer('work_time')->default(0);
            $table->integer('education_time')->default(0);
            $table->date('employment_start_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id','year','leave_type']);
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
