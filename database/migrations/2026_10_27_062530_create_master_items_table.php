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
        Schema::create('master_items', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('item_code', 50)->unique()->index(); // SAP: ItemCode
            $table->string('item_name', 200)->index();          // SAP: ItemName
            $table->string('foreign_name', 200)->nullable();    // SAP: FrgnName
            $table->string('itms_grp_cod', 50)->nullable()->index(); // SAP: ItmsGrpCod
            $table->string('itms_grp_nam', 150)->nullable();
            $table->string('item_class', 50)->nullable()->index();
            $table->string('item_type', 50)->default('Item');   // SAP: ItemType (Item, Labor, Travel)
            $table->string('uom_code', 50)->default('PC');      // SAP: InvntryUom
            $table->string('ugp_code', 50)->nullable();         // SAP: UgpCode (UoM Group)
            $table->string('barcode', 100)->nullable()->index();
            $table->boolean('is_inventory_item')->default(true); // SAP: InvntItem
            $table->boolean('is_sales_item')->default(true);     // SAP: SellItem
            $table->boolean('is_purchase_item')->default(true);  // SAP: PrchseItem
            $table->boolean('is_asset_item')->default(false);    // SAP: AssetItem
            $table->decimal('on_hand', 15, 2)->default(0);       // SAP: OnHand
            $table->decimal('is_committed', 15, 2)->default(0);  // SAP: IsCommited
            $table->decimal('on_order', 15, 2)->default(0);      // SAP: OnOrder
            $table->decimal('cost_price', 15, 4)->default(0);    // SAP: AvgPrice / Cost
            $table->decimal('sales_price', 15, 4)->default(0);   // Standard Price
            $table->string('default_whs', 50)->nullable();       // SAP: DfltWH
            $table->string('status', 30)->default('Active')->index();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_items');
    }
};
