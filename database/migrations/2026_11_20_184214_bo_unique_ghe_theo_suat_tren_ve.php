<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('ve', function (Blueprint $table) {
        $table->dropForeign(['ghe_theo_suat_id']);
        $table->dropUnique(['ghe_theo_suat_id']);
        $table->foreign('ghe_theo_suat_id')->references('id')->on('ghe_theo_suat')->restrictOnDelete();
    });
}

public function down(): void
{
    Schema::table('ve', function (Blueprint $table) {
        $table->dropForeign(['ghe_theo_suat_id']);
        $table->unique('ghe_theo_suat_id');
        $table->foreign('ghe_theo_suat_id')->references('id')->on('ghe_theo_suat')->restrictOnDelete();
    });
}
};
