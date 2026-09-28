<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalPiketKbm extends Model
{
    protected $table = 'jadwal_piket_kbm';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'siklus',
        'hari',
        'shift',
        'jam_mulai',
        'jam_selesai',
        'peran',
        'urutan',
        'id_guru',
        'nama_guru',
        'nip',
        'piket_waka_nama',
        'piket_waka_nip',
    ];

    /**
     * Relationship to Guru
     */
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru', 'id_guru');
    }

    /**
     * Determine cycle ('A' or 'B') for any given Carbon date.
     * Matches the official 2026/2027 rotation schedule of SMKN 1 Boyolangu.
     */
    public static function getSiklusForDate(Carbon $date): string
    {
        // Reference first occurrences in September 2026 (Semester Ganjil 2026/2027)
        $referenceDates = [
            1 => Carbon::create(2026, 9, 7), // Senin (7 Sep 2026) -> Siklus A
            2 => Carbon::create(2026, 9, 1), // Selasa (1 Sep 2026) -> Siklus A
            3 => Carbon::create(2026, 9, 2), // Rabu (2 Sep 2026) -> Siklus A
            4 => Carbon::create(2026, 9, 3), // Kamis (3 Sep 2026) -> Siklus A
            5 => Carbon::create(2026, 9, 4), // Jumat (4 Sep 2026) -> Siklus A
        ];

        $dayOfWeek = $date->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
        if (!isset($referenceDates[$dayOfWeek])) {
            return 'A'; // Default for weekend
        }

        $ref = $referenceDates[$dayOfWeek];
        // Calculate weeks difference between the target date and reference date
        $diffDays = $ref->diffInDays($date, false);
        $diffWeeks = (int) floor($diffDays / 7);

        // If even difference => same cycle ('A'), if odd => alternate ('B')
        return (abs($diffWeeks) % 2 === 0) ? 'A' : 'B';
    }

    /**
     * Get full duty roster for a specific date.
     */
    public static function getRosterForDate(Carbon $date): array
    {
        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $hari = $dayNames[$date->dayOfWeekIso] ?? 'Senin';
        $siklus = self::getSiklusForDate($date);

        if (!in_array($hari, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])) {
            return [
                'hari' => $hari,
                'siklus' => $siklus,
                'is_libur' => true,
                'waka' => null,
                'pagi_koordinator' => null,
                'pagi_petugas' => collect(),
                'siang_koordinator' => null,
                'siang_petugas' => collect(),
            ];
        }

        $schedule = self::where('siklus', $siklus)
            ->where('hari', $hari)
            ->orderBy('urutan')
            ->get();

        $wakaItem = $schedule->first();
        $waka = $wakaItem ? [
            'nama' => $wakaItem->piket_waka_nama,
            'nip' => $wakaItem->piket_waka_nip,
        ] : null;

        $pagi = $schedule->where('shift', 'Pagi');
        $siang = $schedule->where('shift', 'Siang');

        return [
            'hari' => $hari,
            'siklus' => $siklus,
            'is_libur' => false,
            'waka' => $waka,
            'pagi_koordinator' => $pagi->firstWhere('peran', 'koordinator'),
            'pagi_petugas' => $pagi->where('peran', 'petugas')->values(),
            'siang_koordinator' => $siang->firstWhere('peran', 'koordinator'),
            'siang_petugas' => $siang->where('peran', 'petugas')->values(),
        ];
    }
}
