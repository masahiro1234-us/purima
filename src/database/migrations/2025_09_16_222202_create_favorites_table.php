<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade'); // ← 修正ポイント
            $table->timestamps();

            $table->unique(['user_id', 'item_id']); // 二重登録防止
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};