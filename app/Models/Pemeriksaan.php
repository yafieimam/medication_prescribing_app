<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'dokter_id',
        'nama_pasien',
        'waktu_pemeriksaan',
        'tinggi_badan',
        'berat_badan',
        'systole',
        'diastole',
        'heart_rate',
        'respiration_rate',
        'suhu_tubuh',
        'catatan',
        'sudah_dilayani'
    ];

    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }

    public function berkas()
    {
        return $this->hasMany(PemeriksaanBerkas::class);
    }
}
