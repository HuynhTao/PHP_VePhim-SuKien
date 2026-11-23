<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('phim', 'the_loai')) {
            Schema::table('phim', function (Blueprint $table) {
                $table->string('the_loai', 100)->nullable()->after('ngon_ngu');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('phim', 'the_loai')) {
            Schema::table('phim', function (Blueprint $table) {
                $table->dropColumn('the_loai');
            });
        }
    }
};
