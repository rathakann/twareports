<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create or retrieve parent SAP module under Master Data
        $sap = Module::firstOrCreate(
            [
                'group_label' => 'Master Data',
                'label' => 'SAP',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'group_order' => 20,
                'icon' => 'database',
                'route' => null,
                'url' => null,
                'permission' => null,
                'parent_id' => null,
                'sort_order' => 10,
                'is_active' => true,
                'description' => 'SAP Business One Master Data Integration',
            ]
        );

        // 2. Reparent all existing Master Data modules under the SAP parent
        Module::where('group_label', 'Master Data')
            ->where('id', '!=', $sap->id)
            ->update([
                'parent_id' => $sap->id,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sap = Module::where('group_label', 'Master Data')->where('label', 'SAP')->first();

        if ($sap) {
            Module::where('parent_id', $sap->id)->update([
                'parent_id' => null,
            ]);

            $sap->delete();
        }
    }
};
