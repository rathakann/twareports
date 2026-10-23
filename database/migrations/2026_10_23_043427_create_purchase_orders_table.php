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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('doc_num', 50)->unique();
            $table->string('vendor_code', 50)->index();
            $table->string('vendor_name', 150)->index();
            $table->date('doc_date')->index();
            $table->date('delivery_date')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('doc_total', 15, 2)->default(0);
            $table->string('status', 30)->default('Open')->index();
            $table->string('payment_terms', 100)->nullable();
            $table->string('buyer_name', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
