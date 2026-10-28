<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('navigation_groups', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('label', 100)->unique();
            $table->string('icon', 100)->default('folder');
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('modules', function (Blueprint $table): void {
            $table->foreignId('navigation_group_id')
                ->nullable()
                ->after('group_label')
                ->constrained('navigation_groups')
                ->nullOnDelete();
        });

        // Seed initial navigation groups from known defaults and existing modules
        $defaultGroups = [
            ['label' => 'Workspace', 'icon' => 'layout-dashboard', 'sort_order' => 10, 'description' => 'Personal workspace and overview dashboards'],
            ['label' => 'Master Data', 'icon' => 'database', 'sort_order' => 20, 'description' => 'Core ERP and SAP master data catalogs'],
            ['label' => 'Report', 'icon' => 'file-bar-chart', 'sort_order' => 30, 'description' => 'Operational, financial and sales reporting suites'],
            ['label' => 'Access Control', 'icon' => 'shield', 'sort_order' => 40, 'description' => 'User credentials, roles and permission security'],
            ['label' => 'Administrator', 'icon' => 'shield-check', 'sort_order' => 50, 'description' => 'System configuration, navigation and module controls'],
            ['label' => 'Components', 'icon' => 'blocks', 'sort_order' => 60, 'description' => 'Design system and UI component showcase'],
        ];

        $now = now();
        foreach ($defaultGroups as $groupData) {
            $groupId = DB::table('navigation_groups')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'label' => $groupData['label'],
                'icon' => $groupData['icon'],
                'sort_order' => $groupData['sort_order'],
                'is_active' => true,
                'description' => $groupData['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Link existing modules
            DB::table('modules')
                ->where('group_label', $groupData['label'])
                ->update(['navigation_group_id' => $groupId]);
        }

        // Link any remaining modules with unmatched group_label
        $otherGroups = DB::table('modules')
            ->whereNull('navigation_group_id')
            ->whereNotNull('group_label')
            ->distinct()
            ->pluck('group_label');

        $nextOrder = 70;
        foreach ($otherGroups as $otherLabel) {
            $groupId = DB::table('navigation_groups')->insertGetId([
                'uuid' => (string) Str::uuid(),
                'label' => $otherLabel,
                'icon' => 'folder',
                'sort_order' => $nextOrder,
                'is_active' => true,
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('modules')
                ->where('group_label', $otherLabel)
                ->update(['navigation_group_id' => $groupId]);

            $nextOrder += 10;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table): void {
            $table->dropForeign(['navigation_group_id']);
            $table->dropColumn('navigation_group_id');
        });

        Schema::dropIfExists('navigation_groups');
    }
};
