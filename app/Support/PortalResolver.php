<?php

namespace App\Support;

use App\Models\User;
use App\Models\Guru;
use App\Models\Waka;
use App\Models\WaliKelas;
use App\Models\Kelas;
use App\Models\JadwalPiketKbm;
use App\Models\TahunAjaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PortalResolver
{
    /**
     * Seluruh kunci portal yang valid di sistem.
     */
    public const PORTAL_KEYS = [
        'admin',
        'guru_mengajar',
        'wali_kelas',
        'piket',
        'piket_waka',
        'waka_kurikulum',
        'waka_sdm',
        'waka_kesiswaan',
        'satpam',
        'kepala_sekolah',
        'wali_murid',
    ];

    /**
     * Deteksi dan kumpulkan seluruh portal yang dapat diakses oleh user.
     *
     * @param User $user
     * @param Carbon|null $date
     * @return array<string, array>
     */
    public static function resolve(User $user, ?Carbon $date = null): array
    {
        if (!$user->is_active) {
            return [];
        }

        $targetDate = $date ? $date->copy() : Carbon::today();
        $portals = [];
        $portalConfigs = config('portal.portals', []);
        $legacyFallback = config('portal.legacy_role_fallback', true);

        // 1. ADMIN
        if ($user->role === 'admin') {
            $portals['admin'] = self::formatPortalData('admin', $portalConfigs['admin'] ?? []);
        }

        // 2. GURU MENGAJAR (guru.user_id exists atau role guru_mapel) - Selalu aktif
        $hasGuruRecord = $user->guru()->exists();
        if ($hasGuruRecord || $user->role === 'guru_mapel') {
            $portals['guru_mengajar'] = self::formatPortalData('guru_mengajar', $portalConfigs['guru_mengajar'] ?? []);
        }

        // 3. WALI KELAS
        // guru.nip sama dengan wali_kelas.nip yang dipegang minimal satu kelas (kelas.wali_kelas_id -> wali_kelas.id)
        $isWaliKelas = false;
        $userGuru = $user->guru;
        if ($userGuru && $userGuru->nip) {
            $isWaliKelas = Kelas::whereHas('waliKelas', function ($q) use ($userGuru) {
                $q->where('nip', $userGuru->nip);
            })->exists();
        } elseif ($user->waliKelas && Kelas::where('wali_kelas_id', $user->waliKelas->id)->exists()) {
            $isWaliKelas = true;
        }

        // Fallback role legacy
        if (!$isWaliKelas && $legacyFallback && $user->role === 'wali_kelas') {
            $isWaliKelas = true;
        }

        if ($isWaliKelas) {
            $portals['wali_kelas'] = self::formatPortalData('wali_kelas', $portalConfigs['wali_kelas'] ?? []);
        }

        // 4. WAKA_* (waka_kurikulum, waka_sdm, waka_kesiswaan)
        // Hanya jika waka.status_aktif = true dan users.is_active = true
        $userWaka = $user->waka;
        if ($userWaka && $userWaka->status_aktif && $user->is_active) {
            // Verifikasi NIP waka ada di tabel guru
            $nipInGuru = Guru::where('nip', $userWaka->nip)->exists();

            if ($nipInGuru) {
                $bidangKode = $userWaka->bidang_kode;
                if (is_string($bidangKode)) {
                    $bidangKode = json_decode($bidangKode, true);
                }

                if (empty($bidangKode) || !is_array($bidangKode)) {
                    Log::warning("Waka user [ID: {$user->id}, NIP: {$userWaka->nip}] memiliki status_aktif=true namun bidang_kode kosong. Akses portal Waka ditolak.");
                } else {
                    $wakaMap = config('portal.waka_bidang_map', [
                        'kurikulum'    => 'waka_kurikulum',
                        'sdm'          => 'waka_sdm',
                        'kesiswaan'    => 'waka_kesiswaan',
                        'kedisiplinan' => 'waka_kesiswaan',
                    ]);

                    foreach ($bidangKode as $kode) {
                        $targetPortal = $wakaMap[strtolower(trim($kode))] ?? null;
                        if ($targetPortal && !isset($portals[$targetPortal])) {
                            $portals[$targetPortal] = self::formatPortalData(
                                $targetPortal,
                                $portalConfigs[$targetPortal] ?? []
                            );
                        }
                    }
                }
            }
        }

        // Fallback role legacy untuk waka
        if ($legacyFallback) {
            if ($user->role === 'waka_kurikulum' && !isset($portals['waka_kurikulum'])) {
                $portals['waka_kurikulum'] = self::formatPortalData('waka_kurikulum', $portalConfigs['waka_kurikulum'] ?? []);
            }
            if ($user->role === 'waka_sdm' && !isset($portals['waka_sdm'])) {
                $portals['waka_sdm'] = self::formatPortalData('waka_sdm', $portalConfigs['waka_sdm'] ?? []);
            }
            if ($user->role === 'waka_kesiswaan' && !isset($portals['waka_kesiswaan'])) {
                $portals['waka_kesiswaan'] = self::formatPortalData('waka_kesiswaan', $portalConfigs['waka_kesiswaan'] ?? []);
            }
        }

        // 5. PIKET GURU
        // guru.id_guru ada di jadwal_piket_kbm pada tanggal tugas hari ini
        $piketData = self::resolvePiketGuru($user, $targetDate);
        if ($piketData !== null) {
            $configPiket = $portalConfigs['piket'] ?? [];
            $portals['piket'] = self::formatPortalData(
                'piket',
                $configPiket,
                $piketData['badge'],
                $piketData['metadata']
            );
        } elseif ($legacyFallback && $user->role === 'guru_piket') {
            $portals['piket'] = self::formatPortalData(
                'piket',
                $portalConfigs['piket'] ?? [],
                'Shift Pagi (Petugas)'
            );
        }

        // 6. PIKET WAKA
        // guru.nip atau waka.nip sama dengan piket_waka_nip pada jadwal hari ini (Hanya NIP, tanpa nama)
        $piketWakaData = self::resolvePiketWaka($user, $targetDate, $legacyFallback);
        if ($piketWakaData !== null) {
            $portals['piket_waka'] = self::formatPortalData(
                'piket_waka',
                $portalConfigs['piket_waka'] ?? [],
                $piketWakaData['badge'] ?? 'Waka Piket'
            );
        }

        // 7. SATPAM
        if ($user->role === 'satpam') {
            $portals['satpam'] = self::formatPortalData('satpam', $portalConfigs['satpam'] ?? []);
        }

        // 8. KEPALA SEKOLAH
        if ($user->role === 'kepala_sekolah') {
            $portals['kepala_sekolah'] = self::formatPortalData('kepala_sekolah', $portalConfigs['kepala_sekolah'] ?? []);
        }

        // 9. WALI MURID
        if ($user->role === 'wali_murid') {
            $portals['wali_murid'] = self::formatPortalData('wali_murid', $portalConfigs['wali_murid'] ?? []);
        }

        return $portals;
    }

    /**
     * Dapatkan hanya array string kunci portal yang dimiliki user.
     *
     * @param User $user
     * @param Carbon|null $date
     * @return array<string>
     */
    public static function getPortalKeys(User $user, ?Carbon $date = null): array
    {
        return array_keys(self::resolve($user, $date));
    }

    /**
     * Periksa apakah user memiliki akses ke salah satu dari portal yang diminta.
     * Admin selalu memiliki akses ke semua portal.
     *
     * @param User $user
     * @param string|array $requiredPortals
     * @param Carbon|null $date
     * @return bool
     */
    public static function hasAccess(User $user, string|array $requiredPortals, ?Carbon $date = null): bool
    {
        if (!$user->is_active) {
            return false;
        }

        // Admin boleh membuka semua portal
        if ($user->role === 'admin') {
            return true;
        }

        $required = is_array($requiredPortals) ? $requiredPortals : explode(',', $requiredPortals);
        $required = array_map('trim', $required);

        $available = self::getPortalKeys($user, $date);

        foreach ($required as $req) {
            if (in_array($req, $available, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Tentukan portal utama / default bagi user.
     * Mengembalikan null jika user tidak memiliki portal aktif.
     *
     * @param User $user
     * @param Carbon|null $date
     * @return string|null
     */
    public static function getDefaultPortal(User $user, ?Carbon $date = null): ?string
    {
        if ($user->role === 'admin') {
            return 'admin';
        }

        $available = self::resolve($user, $date);
        $keys = array_keys($available);

        if (empty($keys)) {
            return null;
        }

        // Jika user memiliki portal yang identik dengan users.role, prioritaskan itu
        if (in_array($user->role, $keys, true)) {
            return $user->role;
        }

        // Urutan prioritas portal berikutnya
        $priorityOrder = [
            'kepala_sekolah',
            'waka_kurikulum',
            'waka_sdm',
            'waka_kesiswaan',
            'piket_waka',
            'wali_kelas',
            'piket',
            'guru_mengajar',
            'satpam',
            'wali_murid',
        ];

        foreach ($priorityOrder as $portalKey) {
            if (in_array($portalKey, $keys, true)) {
                return $portalKey;
            }
        }

        return $keys[0];
    }

    /**
     * Evaluasi piket guru untuk tanggal tertentu.
     */
    protected static function resolvePiketGuru(User $user, Carbon $date): ?array
    {
        $guru = $user->guru;
        if (!$guru) {
            return null;
        }

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $hari = $dayNames[$date->dayOfWeekIso] ?? null;
        if (!$hari) {
            return null;
        }

        $siklus = JadwalPiketKbm::getSiklusForDate($date);

        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) {
            return null;
        }

        $query = JadwalPiketKbm::where('id_guru', $guru->id_guru)
            ->where('hari', $hari)
            ->where('siklus', $siklus)
            ->where('tahun_ajaran', $taAktif->nama)
            ->where('semester', $taAktif->semester);

        $duty = $query->first();
        if (!$duty) {
            return null;
        }

        // Validasi batasan waktu jika scope='shift'
        $scope = config('portal.piket_scope', 'hari');
        if ($scope === 'shift') {
            $nowTime = Carbon::now()->format('H:i:s');
            if ($duty->jam_mulai && $duty->jam_selesai) {
                if ($nowTime < $duty->jam_mulai || $nowTime > $duty->jam_selesai) {
                    return null;
                }
            }
        }

        $shiftLabel = $duty->shift ? "Shift {$duty->shift}" : 'Piket KBM';
        $peranLabel = $duty->peran ? ucfirst($duty->peran) : 'Petugas';
        $badge = "{$shiftLabel} ({$peranLabel})";

        return [
            'badge'    => $badge,
            'metadata' => [
                'shift'       => $duty->shift,
                'peran'       => $duty->peran,
                'jam_mulai'   => $duty->jam_mulai,
                'jam_selesai' => $duty->jam_selesai,
            ],
        ];
    }

    /**
     * Evaluasi piket waka untuk tanggal tertentu.
     */
    protected static function resolvePiketWaka(User $user, Carbon $date, bool $legacyFallback): ?array
    {
        // 1. Akun bersama meja piket sementara didukung lewat legacy fallback
        // @deprecated Akun bersama meja piket waka.piket@smkn1boyolangu.sch.id (ID 146)
        if ($legacyFallback && ($user->id === 146 || $user->email === 'waka.piket@smkn1boyolangu.sch.id')) {
            return [
                'badge' => 'Waka Piket',
            ];
        }

        if ($legacyFallback && $user->role === 'waka_piket') {
            return [
                'badge' => 'Waka Piket',
            ];
        }

        $userNip = $user->guru->nip ?? ($user->waka->nip ?? null);
        if (!$userNip) {
            return null;
        }

        $dayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $hari = $dayNames[$date->dayOfWeekIso] ?? null;
        if (!$hari) {
            return null;
        }

        $siklus = JadwalPiketKbm::getSiklusForDate($date);
        $taAktif = TahunAjaran::where('is_aktif', true)->first();
        if (!$taAktif) {
            return null;
        }

        $query = JadwalPiketKbm::where('hari', $hari)
            ->where('siklus', $siklus)
            ->where('piket_waka_nip', $userNip)
            ->where('tahun_ajaran', $taAktif->nama)
            ->where('semester', $taAktif->semester);

        $isWakaDuty = $query->exists();

        if ($isWakaDuty) {
            return [
                'badge' => 'Waka Piket',
            ];
        }

        return null;
    }

    /**
     * Format struktur portal dengan metadata lengkap.
     */
    protected static function formatPortalData(
        string $key,
        array $config,
        ?string $badge = null,
        array $metadata = []
    ): array {
        return [
            'key'        => $key,
            'name'       => $config['name'] ?? ucfirst(str_replace('_', ' ', $key)),
            'route'      => $config['route'] ?? 'login',
            'icon'       => $config['icon'] ?? 'fa-solid fa-circle',
            'color'      => $config['color'] ?? '#3b82f6',
            'permission' => $config['permission'] ?? '',
            'badge'      => $badge,
            'metadata'   => $metadata,
        ];
    }
}
