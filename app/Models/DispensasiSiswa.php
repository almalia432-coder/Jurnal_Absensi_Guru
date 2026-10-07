<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensasiSiswa extends Model
{
    protected $table = 'dispensasi_siswa';

    protected $fillable = [
        'id_siswa',
        'tanggal',
        'jam_keluar',
        'jam_kembali',
        'jam_keluar_aktual',
        'jam_kembali_aktual',
        'alasan',
        'bukti_file',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'diinput_oleh',
        'piket_approved_by',
        'piket_at',
        'piket_catatan',
        'catatan_waka',
        'alasan_batal',
        'dibatalkan_oleh',
        'dibatalkan_at',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'piket_at'            => 'datetime',
        'tanggal_persetujuan' => 'datetime',
        'dibatalkan_at'       => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────────────────

    /**
     * Dispensasi yang sudah final (boleh mengunci presensi di jurnal guru mapel).
     * TIDAK termasuk Disetujui_Piket (masih menunggu waka) dan Ditolak.
     */
    public function scopeFinal($query)
    {
        return $query->whereIn('status', [
            'Disetujui',
            'Disetujui_KS',
            'Disetujui_Waka',
            'Selesai',
        ]);
    }

    // ─── Relations ────────────────────────────────────────────

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function diinputOlehUser()
    {
        return $this->belongsTo(User::class, 'diinput_oleh')->withTrashed();
    }

    public function disetujuiOlehUser()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh')->withTrashed();
    }

    public function piketApprovedByUser()
    {
        return $this->belongsTo(User::class, 'piket_approved_by')->withTrashed();
    }

    public function dibatalkanOlehUser()
    {
        return $this->belongsTo(User::class, 'dibatalkan_oleh')->withTrashed();
    }
}
