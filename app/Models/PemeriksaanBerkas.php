<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemeriksaanBerkas extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pemeriksaan_id',
        'file_path'
    ];

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class);
    }
}
