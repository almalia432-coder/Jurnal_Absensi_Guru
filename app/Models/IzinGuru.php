<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IzinGuru extends Model
{
    protected $table = 'izin_guru';

    protected $fillable = [
        'id_guru',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'alasan',
        'bukti_file',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan_persetujuan',
        'diinput_oleh',
        // Multi-level approval fields
        'piket_approved_by',
        'piket_status',
        'piket_at',
        'piket_catatan',
        'waka_approved_by',
        'waka_status',
        'waka_at',
        'waka_catatan',
        'kepsek_approved_by',
        'kepsek_status',
        'kepsek_at',
        'kepsek_catatan',
        'tahap_approval',
        'ditolak_oleh_role',
        'ditolak_catatan',
        // Tugas mandiri
        'menitipkan_tugas',
        'keterangan_tugas',
        'lampiran_tugas',
    ];

    protected $casts = [
        'piket_at'            => 'datetime',
        'waka_at'             => 'datetime',
        'kepsek_at'           => 'datetime',
        'tanggal_persetujuan' => 'datetime',
        'menitipkan_tugas'    => 'boolean',
    ];

    public function hasTugas(): bool
    {
        return (bool) $this->menitipkan_tugas;
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    public function diinputOlehUser()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function disetujuiOlehUser()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function piketApprover()
    {
        return $this->belongsTo(User::class, 'piket_approved_by');
    }

    public function wakaApprover()
    {
        return $this->belongsTo(User::class, 'waka_approved_by');
    }

    public function kepsekApprover()
    {
        return $this->belongsTo(User::class, 'kepsek_approved_by');
    }

    // Helper checks
    public function isPendingPiket(): bool
    {
        return $this->tahap_approval === 'piket' && $this->status !== 'Ditolak';
    }

    public function isPendingWaka(): bool
    {
        return $this->tahap_approval === 'waka_sdm' && $this->status !== 'Ditolak';
    }

    public function isPendingKepsek(): bool
    {
        return $this->tahap_approval === 'kepsek' && $this->status !== 'Ditolak';
    }

    public function isFullyApproved(): bool
    {
        return $this->status === 'Disetujui' && $this->tahap_approval === 'selesai';
    }

    public function isRejected(): bool
    {
        return $this->status === 'Ditolak' || $this->tahap_approval === 'ditolak';
    }

    public function getTahapLabelAttribute(): string
    {
        if ($this->isRejected()) {
            $roleLabel = match ($this->ditolak_oleh_role) {
                'guru_piket'     => 'Guru Piket',
                'waka_sdm'       => 'Waka SDM',
                'kepala_sekolah' => 'Kepala Sekolah',
                default          => 'Pihak Sekolah',
            };
            return 'Ditolak oleh ' . $this->penolak_label;
        }

        if ($this->isFullyApproved()) {
            return 'Disetujui Penuh (Piket, Waka SDM, Kepsek)';
        }

        return match ($this->tahap_approval) {
            'piket'    => 'Menunggu Persetujuan Guru Piket (Tahap 1/3)',
            'waka_sdm' => 'Menunggu Persetujuan Waka SDM (Tahap 2/3)',
            'kepsek'   => 'Menunggu Persetujuan Kepala Sekolah (Tahap 3/3)',
            default    => 'Menunggu Verifikasi',
        };
    }

    public function getPenolakLabelAttribute(): string
    {
        return match ($this->ditolak_oleh_role) {
            'guru_piket'     => 'Guru Piket',
            'waka_sdm'       => 'Waka SDM',
            'kepala_sekolah' => 'Kepala Sekolah',
            default          => 'Pihak Sekolah',
        };
    }
}
