<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinSiswa extends Model
{
    protected $table = 'izin_siswa';

    protected $fillable = [
        'id_siswa',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan',
        'bukti_file',
        'status',
        'diinput_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function diinputOlehUser()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    /**
     * Scope untuk perizinan yang aktif pada tanggal tertentu
     */
    public function scopeActiveOnDate($query, $date)
    {
        return $query->whereDate('tanggal_mulai', '<=', $date)
                     ->whereDate('tanggal_selesai', '>=', $date)
                     ->where('status', '!=', 'Ditolak');
    }
}
