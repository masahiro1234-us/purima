<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->string('avatar_path')->nullable()->after('remember_token');
            $t->string('postal_code', 20)->nullable()->after('avatar_path');
            $t->string('address')->nullable()->after('postal_code');
            $t->string('building')->nullable()->after('address');
            $t->boolean('profile_completed')->default(false)->after('building');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['avatar_path','postal_code','address','building','profile_completed']);
        });
    }
};