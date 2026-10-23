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
        // 1. Balance Sheet Items
        Schema::create('balance_sheet_items', function (Blueprint $table): void {
            $table->id();
            $table->string('account_code', 30)->index();
            $table->string('account_name', 150);
            $table->string('category', 60)->index(); // Current Assets, Non-Current Assets, Current Liabilities, Long-Term Liabilities, Equity
            $table->date('as_of_date')->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('previous_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Profit & Loss Items
        Schema::create('profit_loss_items', function (Blueprint $table): void {
            $table->id();
            $table->string('account_code', 30)->index();
            $table->string('account_name', 150);
            $table->string('category', 60)->index(); // Operating Revenue, Cost of Goods Sold, Operating Expense, Non-Operating, Tax Expense
            $table->date('doc_date')->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('previous_period_amount', 15, 2)->default(0);
            $table->decimal('budget_amount', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('department', 60)->default('Finance');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 3. Cash Flow Entries
        Schema::create('cash_flow_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_num', 40)->index();
            $table->date('doc_date')->index();
            $table->string('activity_type', 60)->index(); // Operating Activities, Investing Activities, Financing Activities
            $table->string('flow_type', 20)->index(); // Inflow, Outflow
            $table->string('description', 200);
            $table->string('account_name', 150);
            $table->decimal('inflow_amount', 15, 2)->default(0);
            $table->decimal('outflow_amount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->string('payment_method', 50)->default('Bank Transfer');
            $table->string('currency', 10)->default('USD');
            $table->timestamps();
        });

        // 4. Trial Balance Accounts
        Schema::create('trial_balance_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('account_code', 30)->index();
            $table->string('account_name', 150);
            $table->string('classification', 40)->index(); // Asset, Liability, Equity, Revenue, Expense
            $table->string('statement_type', 40)->default('Balance Sheet'); // Balance Sheet, Profit & Loss
            $table->date('doc_date')->index();
            $table->decimal('beginning_balance', 15, 2)->default(0);
            $table->decimal('debit_amount', 15, 2)->default(0);
            $table->decimal('credit_amount', 15, 2)->default(0);
            $table->decimal('ending_balance', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('status', 30)->default('Active');
            $table->timestamps();
        });

        // 5. General Ledger Entries
        Schema::create('general_ledger_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('entry_number', 40)->index();
            $table->date('doc_date')->index();
            $table->string('account_code', 30)->index();
            $table->string('account_name', 150);
            $table->string('account_type', 40)->index(); // Asset, Liability, Equity, Revenue, Expense
            $table->string('source_doc_type', 60); // AR Invoice, AP Invoice, Outgoing Payment, Incoming Payment, Journal Voucher
            $table->string('source_doc_num', 50)->nullable();
            $table->text('description');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 10)->default('USD');
            $table->string('posted_by', 100)->default('System');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledger_entries');
        Schema::dropIfExists('trial_balance_accounts');
        Schema::dropIfExists('cash_flow_entries');
        Schema::dropIfExists('profit_loss_items');
        Schema::dropIfExists('balance_sheet_items');
    }
};
