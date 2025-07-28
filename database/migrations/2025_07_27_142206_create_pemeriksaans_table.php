<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('users');
            $table->string('nama_pasien');
            $table->timestamp('waktu_pemeriksaan');
            $table->float('tinggi_badan')->nullable();
            $table->float('berat_badan')->nullable();
            $table->integer('systole')->nullable();
            $table->integer('diastole')->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiration_rate')->nullable();
            $table->float('suhu_tubuh')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('sudah_dilayani')->default(false); // oleh apoteker
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemeriksaans');
    }
};
