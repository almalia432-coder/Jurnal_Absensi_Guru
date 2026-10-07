<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class IzinTerlambat extends Model
{
    use HasFactory;

    protected $table = 'izin_terlambat';

    protected $fillable = [
        'id_siswa',
        'id_kelas',
        'tanggal',
        'jam_masuk',
        'jam_ke_mulai',
        'alasan',
        'status',
        'nomor_surat',
        'diinput_oleh',
        'dikonfirmasi_oleh',
        'dikonfirmasi_at',
        'catatan_konfirmasi',
    ];

    protected $casts = [
        'tanggal'         => 'date',
        'dikonfirmasi_at' => 'datetime',
    ];

    /* ── Aliases for developer convenience ── */
    public function getSiswaIdAttribute()
    {
        return $this->id_siswa;
    }

    public function setSiswaIdAttribute($value)
    {
        $this->attributes['id_siswa'] = $value;
    }

    public function getKelasIdAttribute()
    {
        return $this->id_kelas;
    }

    public function setKelasIdAttribute($value)
    {
        $this->attributes['id_kelas'] = $value;
    }

    /* ── Relasi Eloquent ── */

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function diinputOlehUser()
    {
        return $this->belongsTo(User::class, 'diinput_oleh');
    }

    public function dikonfirmasiOlehUser()
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    /* ── Scopes ── */

    public function scopeMenunggu($query)
    {
        return $query->where('status', 'Menunggu');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'Disetujui');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', Carbon::today());
    }

    public function scopeTanggal($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeUntukKelas($query, $idKelas)
    {
        return $query->where('id_kelas', $idKelas);
    }

    /**
     * Generate Nomor Surat Resmi Otomatis:
     * Contoh: 001/IZIN-TLT/X/2026
     */
    public static function generateNomorSurat(Carbon $date): string
    {
        $year = $date->year;
        $month = $date->month;

        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        $romanMonth = $romanMonths[$month] ?? 'X';

        // Hitung total surat yang disetujui pada bulan & tahun ini
        $countThisMonth = self::whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->whereNotNull('nomor_surat')
            ->count();

        $nextNumber = str_pad($countThisMonth + 1, 3, '0', STR_PAD_LEFT);
        $template = config('presensi.format_nomor_surat_terlambat', '{NOMOR}/IZIN-TLT/{BULAN}/{TAHUN}');

        return str_replace(
            ['{NOMOR}', '{BULAN}', '{TAHUN}'],
            [$nextNumber, $romanMonth, $year],
            $template
        );
    }

    /**
     * Parse string rentang jam pelajaran menjadi array integer jam
     * Contoh: '1-2' -> [1, 2], '3' -> [3], '1-3' -> [1, 2, 3]
     */
    public static function parseJamKeHours(string $jamKe): array
    {
        $jamKe = trim($jamKe);
        if (strpos($jamKe, '-') !== false) {
            $parts = explode('-', $jamKe);
            $start = (int) trim($parts[0]);
            $end = (int) trim($parts[1] ?? $parts[0]);
            if ($start > 0 && $end >= $start) {
                return range($start, $end);
            }
        } elseif (strpos($jamKe, ',') !== false) {
            return array_map('intval', explode(',', $jamKe));
        } elseif (is_numeric($jamKe)) {
            return [(int) $jamKe];
        }

        preg_match_all('/\d+/', $jamKe, $matches);
        if (!empty($matches[0])) {
            if (count($matches[0]) === 1) {
                return [(int) $matches[0][0]];
            }
            $start = (int) $matches[0][0];
            $end = (int) end($matches[0]);
            if ($start > 0 && $end >= $start) {
                return range($start, $end);
            }
        }

        return [1];
    }

    /**
     * Hitung status presensi siswa terlambat berdasarkan jam sesi KBM
     * 
     * Aturan Bisnis:
     * - Jam KBM memuat jam_ke_mulai -> Terlambat
     * - Jam KBM sebelum jam_ke_mulai -> Jam terlewat (Alpha sesuai config presensi.jam_terlewat_terlambat)
     * - Jam KBM setelah jam_ke_mulai -> Hadir biasa
     */
    public function resolveStatusForTeachingHour(string $sessionJamKe): array
    {
        $hours = self::parseJamKeHours($sessionJamKe);
        $mulai = (int) $this->jam_ke_mulai;

        // Jika rentang jam KBM memuat jam_ke_mulai
        if (in_array($mulai, $hours)) {
            return [
                'status'     => 'Terlambat',
                'keterangan' => "(Terlambat: Masuk Jam Ke-{$mulai}) " . ($this->alasan ?: ''),
                'is_locked'  => true,
            ];
        }

        // Jika seluruh jam KBM berlangsung SEBELUM siswa tiba (jam terlewat)
        if (max($hours) < $mulai) {
            $statusTerlewat = config('presensi.jam_terlewat_terlambat', 'Alpha');
            return [
                'status'     => $statusTerlewat,
                'keterangan' => "(Terlambat: Izin Masuk Jam Ke-{$mulai}, Terlewat Sesi Ini) " . ($this->alasan ?: ''),
                'is_locked'  => true,
            ];
        }

        // Jika jam KBM berlangsung SETELAH siswa tiba (siswa sudah hadir di sekolah)
        return [
            'status'     => 'Hadir',
            'keterangan' => null,
            'is_locked'  => false,
        ];
    }
}
