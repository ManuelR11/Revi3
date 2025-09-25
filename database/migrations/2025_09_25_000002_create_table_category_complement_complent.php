<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_complement_complement', function (Blueprint $table) {
            $table->unsignedBigInteger('category_complement_id');
            $table->unsignedBigInteger('complement_id');

            $table->foreign('category_complement_id')
                ->references('id')
                ->on('category_complements')
                ->onDelete('cascade');

            $table->foreign('complement_id')
                ->references('id')
                ->on('complements')
                ->onDelete('cascade');

            $table->primary(['category_complement_id', 'complement_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_complement_complement');
    }
};
