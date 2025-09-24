<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (!Schema::hasColumn('offers', 'combo_item_id')) {
                $table->foreignId('combo_item_id')
                    ->nullable()
                    ->after('combo_price')
                    ->constrained('items')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            if (Schema::hasColumn('offers', 'combo_item_id')) {
                $table->dropForeign(['combo_item_id']);
                $table->dropColumn('combo_item_id');
            }
        });
    }
};