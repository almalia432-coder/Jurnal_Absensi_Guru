<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalMengajar extends Model
{
    use SoftDeletes;

    protected $table = 'jurnal_mengajar';

    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'id_jadwal',
        'id_guru',
        'id_kelas',
        'id_mapel',
        'tanggal',
        'jam_ke',
        'jam_mulai',
        'jam_selesai',
        'materi',
        'jumlah_siswa_hadir',
        'jumlah_siswa_tidak_hadir',
        'status_guru',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'id_jadwal', 'id_jadwal');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'id_mapel', 'id_mapel');
    }

    public function presensiSiswa()
    {
        return $this->hasMany(PresensiSiswa::class, 'id_jurnal', 'id_jurnal');
    }

    /**
     * Menentukan status sesi KBM secara dinamis berdasarkan jam & tanggal.
     */
    public function getStatusJurnalAttribute(): string
    {
        if (empty($this->materi) && $this->jumlah_siswa_hadir == 0 && $this->jumlah_siswa_tidak_hadir == 0) {
            return 'Belum Selesai';
        }

        $now = \Carbon\Carbon::now();
        $tanggalStr = $this->tanggal ? \Carbon\Carbon::parse($this->tanggal)->format('Y-m-d') : null;
        $todayStr = $now->format('Y-m-d');

        // Jika tanggal KBM adalah hari-hari sebelumnya
        if ($tanggalStr && $tanggalStr < $todayStr) {
            return 'Selesai';
        }

        // Jika tanggal KBM adalah hari esok / masa depan
        if ($tanggalStr && $tanggalStr > $todayStr) {
            return 'Belum Selesai';
        }

        // Jika tanggal KBM adalah hari ini
        if ($tanggalStr === $todayStr) {
            if ($this->jam_selesai) {
                $selesaiCarbon = \Carbon\Carbon::parse($todayStr . ' ' . $this->jam_selesai);
                if ($now->gte($selesaiCarbon)) {
                    return 'Selesai';
                }
            }

            if ($this->jam_mulai && $this->jam_selesai) {
                $mulaiCarbon = \Carbon\Carbon::parse($todayStr . ' ' . $this->jam_mulai);
                $selesaiCarbon = \Carbon\Carbon::parse($todayStr . ' ' . $this->jam_selesai);
                if ($now->between($mulaiCarbon, $selesaiCarbon)) {
                    return 'Sedang Berlangsung';
                }
                if ($now->lt($mulaiCarbon)) {
                    return 'Belum Selesai';
                }
            }
        }

        return 'Selesai';
    }
}