<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dia_diem', function (Blueprint $table) {
            $table->id();
            $table->string('ten_dia_diem');
            $table->string('dia_chi');
            $table->string('tinh_thanh', 100);
            $table->string('so_dien_thoai', 20)->nullable();
            $table->enum('trang_thai', ['hoat_dong', 'tam_dung'])->default('hoat_dong');
            $table->timestamps();
        });
        Schema::create('phong_chieu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dia_diem_id')->constrained('dia_diem')->restrictOnDelete();
            $table->string('ten_phong', 100);
            $table->string('loai_phong', 50)->default('2D');
            $table->enum('trang_thai', ['hoat_dong', 'bao_tri'])->default('hoat_dong');
            $table->timestamps();
            $table->unique(['dia_diem_id', 'ten_phong']);
        });
        Schema::create('ghe_ngoi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phong_chieu_id')->constrained('phong_chieu')->restrictOnDelete();
            $table->string('hang_ghe', 10);
            $table->unsignedSmallInteger('so_ghe');
            $table->enum('loai_ghe', ['thuong', 'vip'])->default('thuong');
            $table->boolean('dang_su_dung')->default(true);
            $table->timestamps();
            $table->unique(['phong_chieu_id', 'hang_ghe', 'so_ghe']);
        });
        Schema::create('noi_dung', function (Blueprint $table) {
            $table->id();
            $table->enum('loai_noi_dung', ['phim', 'su_kien'])->index();
            $table->string('tieu_de');
            $table->text('mo_ta')->nullable();
            $table->string('anh_bia')->nullable();
            $table->enum('trang_thai', ['nhap', 'cong_bo', 'an'])->default('nhap')->index();
            $table->timestamps();
        });
        Schema::create('phim', function (Blueprint $table) {
            $table->foreignId('noi_dung_id')->primary()->constrained('noi_dung')->cascadeOnDelete();
            $table->unsignedSmallInteger('thoi_luong_phut');
            $table->string('gioi_han_tuoi', 20)->default('P');
            $table->string('ngon_ngu', 100);
            $table->date('ngay_khoi_chieu');
            $table->timestamps();
        });
        Schema::create('su_kien', function (Blueprint $table) {
            $table->foreignId('noi_dung_id')->primary()->constrained('noi_dung')->cascadeOnDelete();
            $table->string('don_vi_to_chuc');
            $table->string('the_loai', 100);
            $table->string('gioi_han_tuoi', 20)->default('P');
            $table->timestamps();
        });
        Schema::create('suat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('noi_dung_id')->constrained('noi_dung')->restrictOnDelete();
            $table->foreignId('phong_chieu_id')->constrained('phong_chieu')->restrictOnDelete();
            $table->dateTime('bat_dau_luc')->index();
            $table->dateTime('ket_thuc_luc');
            $table->enum('trang_thai', ['mo_ban', 'dong', 'huy'])->default('mo_ban');
            $table->timestamps();
            $table->unique(['phong_chieu_id', 'bat_dau_luc']);
        });
        Schema::create('ghe_theo_suat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suat_id')->constrained('suat')->restrictOnDelete();
            $table->foreignId('ghe_ngoi_id')->constrained('ghe_ngoi')->restrictOnDelete();
            $table->decimal('gia_ve', 12, 2);
            $table->enum('trang_thai', ['trong', 'dang_giu', 'da_ban', 'tam_khoa'])->default('trong');
            $table->dateTime('giu_den_luc')->nullable()->index();
            $table->timestamps();
            $table->unique(['suat_id', 'ghe_ngoi_id']);
        });
        Schema::create('don_hang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->restrictOnDelete();
            $table->string('ma_don_hang', 50)->unique();
            $table->enum('trang_thai', ['cho_thanh_toan', 'da_thanh_toan', 'da_huy', 'het_han'])->default('cho_thanh_toan');
            $table->decimal('tam_tinh', 12, 2);
            $table->decimal('tien_giam', 12, 2)->default(0);
            $table->decimal('tong_tien', 12, 2);
            $table->dateTime('het_han_luc')->nullable();
            $table->dateTime('dat_luc');
            $table->timestamps();
        });
        Schema::create('ve', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->restrictOnDelete();
            $table->foreignId('ghe_theo_suat_id')->unique()->constrained('ghe_theo_suat')->restrictOnDelete();
            $table->string('ma_ve', 50)->unique();
            $table->enum('trang_thai', ['hieu_luc', 'da_su_dung', 'da_huy'])->default('hieu_luc');
            $table->decimal('gia_ban', 12, 2);
            $table->dateTime('phat_hanh_luc');
            $table->timestamps();
        });
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->restrictOnDelete();
            $table->string('nha_cung_cap', 50);
            $table->string('ma_giao_dich', 100)->nullable();
            $table->decimal('so_tien', 12, 2);
            $table->enum('trang_thai', ['cho_xu_ly', 'thanh_cong', 'that_bai'])->default('cho_xu_ly');
            $table->dateTime('thanh_toan_luc')->nullable();
            $table->json('du_lieu_phan_hoi')->nullable();
            $table->timestamps();
            $table->unique(['nha_cung_cap', 'ma_giao_dich']);
        });
    }

    public function down(): void
    {
        foreach (['thanh_toan', 've', 'don_hang', 'ghe_theo_suat', 'suat', 'su_kien', 'phim', 'noi_dung', 'ghe_ngoi', 'phong_chieu', 'dia_diem'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
