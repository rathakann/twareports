<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * The tables that should have a UUID column.
     *
     * @var list<string>
     */
    protected array $tables = [
        'users',
        'roles',
        'permissions',
        'reports',
        'purchase_orders',
        'ap_invoices',
        'outgoing_payments',
        'sales_purchase_orders',
        'ar_invoices',
        'incoming_payments',
        'deposits',
        'balance_sheet_items',
        'profit_loss_items',
        'cash_flow_entries',
        'trial_balance_accounts',
        'general_ledger_entries',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            if (! Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->uuid('uuid')->nullable()->after('id');
                });
            }

            if (DB::table($tableName)->whereNull('uuid')->exists()) {
                if (DB::getDriverName() === 'mysql') {
                    DB::statement("UPDATE `{$tableName}` SET `uuid` = UUID() WHERE `uuid` IS NULL");
                } else {
                    DB::table($tableName)->whereNull('uuid')->select('id')->chunkById(200, function ($rows) use ($tableName): void {
                        foreach ($rows as $row) {
                            DB::table($tableName)->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]);
                        }
                    });
                }
            }

            try {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->unique('uuid');
                });
            } catch (Throwable $e) {
                // Unique index may already exist if previously created
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'uuid')) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};
