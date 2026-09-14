<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Siswa extends Model
{
    use SoftDeletes;

    protected $table = 'siswa';

    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'niss',
        'nama_lengkap',
        'jenis_kelamin',
        'id_kelas',
        'no_hp_ortu',
        'alamat',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function presensi()
    {
        return $this->hasMany(PresensiSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function dispensasi()
    {
        return $this->hasMany(DispensasiSiswa::class, 'id_siswa', 'id_siswa');
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}
