<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incoming_payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_num', 50)->unique();
            $table->date('doc_date')->index();
            $table->string('customer_code', 50)->index();
            $table->string('customer_name', 150)->index();
            $table->string('payment_method', 50)->default('Bank Transfer')->index();
            $table->string('reference_num', 100)->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('amount_received', 15, 2)->default(0);
            $table->string('bank_account', 100)->nullable();
            $table->string('status', 30)->default('Cleared')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_payments');
    }
};
