<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guru extends Model
{
    use SoftDeletes;

    protected $table = 'guru';

    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'no_hp',
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

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'id_guru', 'id_guru');
    }

    public function jurnalMengajar()
    {
        return $this->hasMany(JurnalMengajar::class, 'id_guru', 'id_guru');
    }

    public function izinGuru()
    {
        return $this->hasMany(IzinGuru::class, 'id_guru', 'id_guru');
    }

    /**
     * @deprecated Relasi ini membandingkan wali_kelas_id dengan id_guru yang salah.
     * Gunakan PortalResolver atau kelasWaliValid() yang mencocokkan NIP ke wali_kelas.
     */
    public function kelasWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id', 'id_guru');
    }

    /**
     * Relasi kelas binaan yang valid melalui tabel wali_kelas (berdasarkan NIP).
     */
    public function kelasWaliValid()
    {
        return $this->hasManyThrough(
            Kelas::class,
            WaliKelas::class,
            'nip',            // Foreign key pada wali_kelas yang cocok dengan guru.nip
            'wali_kelas_id',  // Foreign key pada kelas yang cocok dengan wali_kelas.id
            'nip',            // Local key pada guru
            'id'              // Local key pada wali_kelas
        );
    }

    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }
}