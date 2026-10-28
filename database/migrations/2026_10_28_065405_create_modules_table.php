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
        Schema::create('modules', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('group_label', 100)->default('General')->index();
            $table->integer('group_order')->default(0)->index();
            $table->string('label', 150)->index();
            $table->string('icon', 100)->default('circle');
            $table->string('route', 150)->nullable()->index();
            $table->string('url', 255)->nullable();
            $table->string('permission', 150)->nullable()->index();
            $table->foreignId('parent_id')->nullable()->constrained('modules')->nullOnDelete();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->string('badge', 50)->nullable();
            $table->string('target', 20)->default('_self');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
