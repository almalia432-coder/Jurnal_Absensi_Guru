<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kelas extends Model
{
    use SoftDeletes;

    protected $table = 'kelas';

    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'id_jurusan',
        'wali_kelas_id',
        'jumlah_siswa',
    ];

    public function jurusanRelation()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan', 'id_jurusan');
    }

    public function waliKelas()
    {
        return $this->belongsTo(WaliKelas::class, 'wali_kelas_id', 'id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'id_kelas', 'id_kelas');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_kelas', 'id_kelas');
    }

    public function jurnalMengajar()
    {
        return $this->hasMany(JurnalMengajar::class, 'id_kelas', 'id_kelas');
    }

    public function izinTerlambat()
    {
        return $this->hasMany(IzinTerlambat::class, 'id_kelas', 'id_kelas');
    }
}