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
        Schema::create('ar_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_num', 50)->unique();
            $table->string('order_reference', 50)->nullable()->index();
            $table->string('customer_code', 50)->index();
            $table->string('customer_name', 150)->index();
            $table->date('doc_date')->index();
            $table->date('due_date')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('doc_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);
            $table->string('status', 30)->default('Unpaid')->index();
            $table->string('sales_employee', 100)->nullable();
            $table->string('payment_terms', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ar_invoices');
    }
};
