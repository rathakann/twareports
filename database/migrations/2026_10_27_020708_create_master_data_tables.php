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
        // 1. Branches (SAP: OBPL - Business Places)
        Schema::create('master_branches', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bpl_id', 50)->unique();
            $table->string('branch_name', 150)->index();
            $table->string('branch_name_en', 150)->nullable();
            $table->string('tax_id', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('street', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('country', 50)->default('Cambodia');
            $table->string('status', 30)->default('Active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 2. Warehouses (SAP: OWHS)
        Schema::create('master_warehouses', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('whs_code', 50)->unique();
            $table->string('whs_name', 150)->index();
            $table->string('branch_code', 50)->nullable()->index();
            $table->string('location', 150)->nullable();
            $table->boolean('is_bin_act')->default(false);
            $table->boolean('is_drop_ship')->default(false);
            $table->boolean('is_net_whs')->default(true);
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 3. Bins (SAP: OBIN - Bin Locations)
        Schema::create('master_bins', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('bin_code', 100)->unique();
            $table->string('whs_code', 50)->index();
            $table->string('aisle', 50)->nullable();
            $table->string('shelf', 50)->nullable();
            $table->string('level', 50)->nullable();
            $table->string('barcode', 100)->nullable()->index();
            $table->decimal('max_weight', 12, 2)->nullable();
            $table->string('status', 30)->default('Active')->index();
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
        });

        // 4. Business Partners (SAP: OCRD)
        Schema::create('master_business_partners', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('card_code', 50)->unique();
            $table->string('card_name', 200)->index();
            $table->string('card_type', 30)->index(); // Customer, Vendor, Lead
            $table->string('group_name', 100)->nullable()->index();
            $table->string('currency', 10)->default('USD');
            $table->string('phone', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->string('payment_terms', 100)->nullable();
            $table->string('vat_num', 100)->nullable();
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 5. Units of Measure (SAP: OUOM)
        Schema::create('master_uoms', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('uom_code', 50)->unique();
            $table->string('uom_name', 150)->index();
            $table->string('intl_code', 50)->nullable();
            $table->decimal('volume', 12, 4)->nullable();
            $table->string('vol_unit', 50)->nullable();
            $table->decimal('weight', 12, 4)->nullable();
            $table->string('weight_unit', 50)->nullable();
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 6. UoM Groups (SAP: OUGP)
        Schema::create('master_uom_groups', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('ugp_code', 50)->unique();
            $table->string('ugp_name', 150)->index();
            $table->string('base_uom_code', 50)->index();
            $table->string('base_uom_name', 100)->nullable();
            $table->integer('alt_uoms_count')->default(1);
            $table->string('status', 30)->default('Active')->index();
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
        });

        // 7. Item Groups (SAP: OITB)
        Schema::create('master_item_groups', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('itms_grp_cod', 50)->unique();
            $table->string('itms_grp_nam', 150)->index();
            $table->string('category', 100)->nullable()->index();
            $table->string('default_whs', 50)->nullable();
            $table->string('valuation_method', 50)->default('Moving Average');
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 8. Item Classes (Material / Asset / Item Classes)
        Schema::create('master_item_classes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('class_code', 50)->unique();
            $table->string('class_name', 150)->index();
            $table->string('item_type', 50)->default('Inventory');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 9. Barcodes (SAP: OBCD)
        Schema::create('master_barcodes', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('item_code', 50)->index();
            $table->string('item_name', 200)->index();
            $table->string('barcode', 100)->unique();
            $table->string('uom_code', 50)->nullable();
            $table->string('barcode_type', 50)->default('EAN13');
            $table->boolean('is_default')->default(true);
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 10. BOM (SAP: OITT - Bill of Materials / Product Tree)
        Schema::create('master_boms', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('tree_code', 50)->unique();
            $table->string('tree_name', 200)->index();
            $table->string('tree_type', 50)->default('Assembly'); // Assembly, Production, Sales, Template
            $table->decimal('output_qty', 12, 4)->default(1.0000);
            $table->string('uom_code', 50)->default('SET');
            $table->string('whs_code', 50)->nullable();
            $table->integer('components_count')->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->string('status', 30)->default('Active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 11. Documents Master (SAP: ONNM - Document Types)
        Schema::create('master_documents', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('object_code', 20)->unique(); // "22" = PO, "18" = AP Invoice, etc.
            $table->string('doc_name', 150)->index();
            $table->string('sap_table_name', 50)->index(); // OPOR, OPCH, OINV, etc.
            $table->string('category', 50)->index(); // Purchasing, Sales, Banking, Inventory
            $table->boolean('has_series')->default(true);
            $table->integer('series_count')->default(1);
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });

        // 12. Document Numbering Series (SAP: NNM1)
        Schema::create('master_document_series', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('object_code', 20)->index(); // Links to master_documents.object_code
            $table->string('doc_name', 150)->index();
            $table->string('series_code', 50)->index();
            $table->string('series_name', 150)->index();
            $table->string('prefix', 50)->nullable();
            $table->string('suffix', 50)->nullable();
            $table->bigInteger('initial_num')->default(1);
            $table->bigInteger('next_num')->default(1);
            $table->bigInteger('last_num')->default(999999);
            $table->string('period_indicator', 50)->default('2026');
            $table->string('branch_code', 50)->nullable()->default('All Branches');
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_manual')->default(false);
            $table->string('status', 30)->default('Active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_document_series');
        Schema::dropIfExists('master_documents');
        Schema::dropIfExists('master_boms');
        Schema::dropIfExists('master_barcodes');
        Schema::dropIfExists('master_item_classes');
        Schema::dropIfExists('master_item_groups');
        Schema::dropIfExists('master_uom_groups');
        Schema::dropIfExists('master_uoms');
        Schema::dropIfExists('master_business_partners');
        Schema::dropIfExists('master_bins');
        Schema::dropIfExists('master_warehouses');
        Schema::dropIfExists('master_branches');
    }
};
