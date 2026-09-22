<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusHarianKbm extends Model
{
    use HasFactory;

    protected $table = 'status_harian_kbm';
    protected $primaryKey = 'id_status';

    protected $fillable = [
        'tanggal',
        'ada_upacara',
        'ada_pembiasaan_jumat',
        'catatan',
        'id_guru_piket',
    ];

    protected $casts = [
        'tanggal'              => 'date',
        'ada_upacara'          => 'boolean',
        'ada_pembiasaan_jumat' => 'boolean',
    ];

    public function guruPiket()
    {
        return $this->belongsTo(Guru::class, 'id_guru_piket', 'id_guru');
    }

    /**
     * Dapatkan status efektif KBM untuk tanggal tertentu (default hari ini).
     * Jika belum ada data di database, kembalikan objek default (Upacara/Pembiasaan aktif).
     */
    public static function getEffectiveStatus(?string $tanggal = null): self
    {
        $date = $tanggal ? Carbon::parse($tanggal)->toDateString() : Carbon::today()->toDateString();

        $status = self::where('tanggal', $date)->first();

        if (!$status) {
            $status = new self([
                'tanggal'              => $date,
                'ada_upacara'          => true,
                'ada_pembiasaan_jumat' => true,
                'catatan'              => null,
                'id_guru_piket'        => null,
            ]);
        }

        return $status;
    }

    /**
     * Cek apakah pembiasaan/upacara hari ini ditiadakan (Jam KBM Maju ke 07.00).
     */
    public static function isMaju(?string $tanggal = null): bool
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $isSenin = ($date->dayOfWeek === Carbon::MONDAY);
        $isJumat = ($date->dayOfWeek === Carbon::FRIDAY);
        $status = self::getEffectiveStatus($date->toDateString());

        if ($isSenin) {
            return !$status->ada_upacara;
        }

        if ($isJumat) {
            return !$status->ada_pembiasaan_jumat;
        }

        return false;
    }

    /**
     * Dapatkan waktu slot efektif jam pelajaran berdasarkan kondisi upacara/pembiasaan hari ini.
     * Jam istirahat dipastikan tetap sama baik upacara/pembiasaan diadakan maupun ditiadakan.
     */
    public static function getTimeSlot(string $hari, int $jam_ke, ?string $tanggal = null): array
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::today();
        $status = self::getEffectiveStatus($date->toDateString());
        $hariLower = strtolower($hari);

        // HARI SENIN
        if ($hariLower === 'senin' || $hariLower === 'monday') {
            if ($status->ada_upacara) {
                // Senin Normal (Ada Upacara 07.00 - 07.40, KBM mulai jam 2 pukul 07.40)
                $slots = [
                    1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'], // Upacara Bendera
                    2  => ['jam_mulai' => '07:40', 'jam_selesai' => '08:20'],
                    3  => ['jam_mulai' => '08:20', 'jam_selesai' => '09:00'],
                    4  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:40'],
                    // Istirahat 1: 09:40 - 09:55
                    5  => ['jam_mulai' => '09:55', 'jam_selesai' => '10:35'],
                    6  => ['jam_mulai' => '10:35', 'jam_selesai' => '11:15'],
                    7  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:55'],
                    // Istirahat 2 (Dhuhur): 11:55 - 12:35
                    8  => ['jam_mulai' => '12:35', 'jam_selesai' => '13:15'],
                    9  => ['jam_mulai' => '13:15', 'jam_selesai' => '13:55'],
                    10 => ['jam_mulai' => '13:55', 'jam_selesai' => '14:35'],
                    11 => ['jam_mulai' => '14:35', 'jam_selesai' => '15:15'],
                    12 => ['jam_mulai' => '15:15', 'jam_selesai' => '15:55'],
                ];
                return $slots[$jam_ke] ?? ['jam_mulai' => '07:40', 'jam_selesai' => '08:20'];
            } else {
                // Senin Tanpa Upacara (Jam ke-2 maju ke 07.00, istirahat tetap di 09.40 dan 11.55)
                $slots = [
                    1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'],
                    2  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'], // Maju 1 slot
                    3  => ['jam_mulai' => '07:40', 'jam_selesai' => '08:20'],
                    4  => ['jam_mulai' => '08:20', 'jam_selesai' => '09:00'],
                    5  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:40'], // Maju sebelum istirahat 1
                    // Istirahat 1: 09:40 - 09:55 (TETAP SAMA)
                    6  => ['jam_mulai' => '09:55', 'jam_selesai' => '10:35'],
                    7  => ['jam_mulai' => '10:35', 'jam_selesai' => '11:15'],
                    8  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:55'], // Maju sebelum istirahat 2
                    // Istirahat 2 (Dhuhur): 11:55 - 12:35 (TETAP SAMA)
                    9  => ['jam_mulai' => '12:35', 'jam_selesai' => '13:15'],
                    10 => ['jam_mulai' => '13:15', 'jam_selesai' => '13:55'],
                    11 => ['jam_mulai' => '13:55', 'jam_selesai' => '14:35'],
                    12 => ['jam_mulai' => '14:35', 'jam_selesai' => '15:15'],
                ];
                return $slots[$jam_ke] ?? ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'];
            }
        }

        // HARI JUMAT
        if ($hariLower === 'jumat' || $hariLower === 'friday') {
            if ($status->ada_pembiasaan_jumat) {
                // Jumat Normal (Ada Pembiasaan 07.00 - 07.30, KBM mulai jam 2 pukul 07.30)
                $slots = [
                    1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'], // Pembiasaan (Jumat Bersih/Religi)
                    2  => ['jam_mulai' => '07:30', 'jam_selesai' => '08:00'],
                    3  => ['jam_mulai' => '08:00', 'jam_selesai' => '08:30'],
                    4  => ['jam_mulai' => '08:30', 'jam_selesai' => '09:00'],
                    5  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:30'],
                    // Istirahat 1: 09:30 - 09:45
                    6  => ['jam_mulai' => '09:45', 'jam_selesai' => '10:15'],
                    7  => ['jam_mulai' => '10:15', 'jam_selesai' => '10:45'],
                    8  => ['jam_mulai' => '10:45', 'jam_selesai' => '11:15'],
                    9  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:45'],
                    // Istirahat 2 / Sholat Jumat: 11:45 - 13:00
                    10 => ['jam_mulai' => '13:00', 'jam_selesai' => '13:30'],
                ];
                return $slots[$jam_ke] ?? ['jam_mulai' => '07:30', 'jam_selesai' => '08:00'];
            } else {
                // Jumat Tanpa Pembiasaan (Jam ke-2 maju ke 07.00, istirahat tetap di 09.30 dan Sholat Jumat di 11.45)
                $slots = [
                    1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'],
                    2  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'], // Maju 1 slot
                    3  => ['jam_mulai' => '07:30', 'jam_selesai' => '08:00'],
                    4  => ['jam_mulai' => '08:00', 'jam_selesai' => '08:30'],
                    5  => ['jam_mulai' => '08:30', 'jam_selesai' => '09:00'],
                    6  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:30'], // Maju sebelum istirahat
                    // Istirahat 1: 09:30 - 09:45 (TETAP SAMA)
                    7  => ['jam_mulai' => '09:45', 'jam_selesai' => '10:15'],
                    8  => ['jam_mulai' => '10:15', 'jam_selesai' => '10:45'],
                    9  => ['jam_mulai' => '10:45', 'jam_selesai' => '11:15'],
                    10 => ['jam_mulai' => '11:15', 'jam_selesai' => '11:45'], // Selesai sebelum Sholat Jumat
                    // Sholat Jumat / Istirahat: 11:45 - 13:00 (TETAP SAMA)
                ];
                return $slots[$jam_ke] ?? ['jam_mulai' => '07:00', 'jam_selesai' => '07:30'];
            }
        }

        // HARI LAINNYA (Selasa, Rabu, Kamis, Sabtu) - Jadwal Reguler Standar
        $regularSlots = [
            1  => ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'],
            2  => ['jam_mulai' => '07:40', 'jam_selesai' => '08:20'],
            3  => ['jam_mulai' => '08:20', 'jam_selesai' => '09:00'],
            4  => ['jam_mulai' => '09:00', 'jam_selesai' => '09:40'],
            // Istirahat 1: 09:40 - 09:55
            5  => ['jam_mulai' => '09:55', 'jam_selesai' => '10:35'],
            6  => ['jam_mulai' => '10:35', 'jam_selesai' => '11:15'],
            7  => ['jam_mulai' => '11:15', 'jam_selesai' => '11:55'],
            // Istirahat 2: 11:55 - 12:35
            8  => ['jam_mulai' => '12:35', 'jam_selesai' => '13:15'],
            9  => ['jam_mulai' => '13:15', 'jam_selesai' => '13:55'],
            10 => ['jam_mulai' => '13:55', 'jam_selesai' => '14:35'],
            11 => ['jam_mulai' => '14:35', 'jam_selesai' => '15:15'],
            12 => ['jam_mulai' => '15:15', 'jam_selesai' => '15:55'],
        ];

        return $regularSlots[$jam_ke] ?? ['jam_mulai' => '07:00', 'jam_selesai' => '07:40'];
    }
}
