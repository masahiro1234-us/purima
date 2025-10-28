<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // 商品名
            $table->unsignedInteger('price');        // 価格（円）
            $table->string('brand')->nullable();     // ブランド名
            $table->text('description');             // 商品説明
            $table->string('img_url');               // 画像URL（S3）
            $table->string('condition');             // コンディション（良好/汚れあり…等）
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('items');
    }
};