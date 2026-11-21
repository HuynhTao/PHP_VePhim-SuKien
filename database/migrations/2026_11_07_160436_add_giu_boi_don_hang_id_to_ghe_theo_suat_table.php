<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ghe_theo_suat', function (Blueprint $table) {
            $table->foreignId('giu_boi_don_hang_id')->nullable()->after('giu_den_luc')
                ->constrained('don_hang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ghe_theo_suat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('giu_boi_don_hang_id');
        });
    }
};