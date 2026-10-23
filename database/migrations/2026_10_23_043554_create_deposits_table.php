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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->string('deposit_num', 50)->unique();
            $table->date('deposit_date')->index();
            $table->string('deposit_type', 50)->default('Cash')->index();
            $table->string('bank_name', 100);
            $table->string('bank_account', 100);
            $table->string('currency', 10)->default('USD');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('reconciliation_status', 30)->default('Unreconciled')->index();
            $table->string('status', 30)->default('Posted')->index();
            $table->string('journal_ref', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
