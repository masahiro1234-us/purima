<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('items', function (Blueprint $table) {
        $table->string('status')->default('on_sale')->after('price');
        // 例: 'on_sale' = 出品中, 'sold' = 売却済み
    });
}

public function down()
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
}
