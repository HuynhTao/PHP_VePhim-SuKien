<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('users', 'nguoi_dung');
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->renameColumn('name', 'ho_ten');
            $table->renameColumn('password', 'mat_khau');
        });
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->string('so_dien_thoai', 20)->nullable();
            $table->enum('vai_tro', ['admin', 'user'])->default('user')->index();
        });
    }

    public function down(): void
    {
        Schema::table('nguoi_dung', function (Blueprint $table) {
            $table->dropColumn(['so_dien_thoai', 'vai_tro']);
            $table->renameColumn('ho_ten', 'name');
            $table->renameColumn('mat_khau', 'password');
        });
        Schema::rename('nguoi_dung', 'users');
    }
};
