<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            // Pastikan daftar di dalam kurung siku sesuai dengan kebutuhan aplikasimu
            $table->enum('status', ['aktif', 'arsip', 'pending'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('dokumen', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'arsip'])->default('aktif')->change();
        });
    }
};