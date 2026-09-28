<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalPiketKbm;
use App\Models\Guru;

class JadwalPiketKbmSeeder extends Seeder
{
    public function run(): void
    {
        JadwalPiketKbm::truncate();

        $rosterData = [
            // ================= SIKLUS A =================
            [
                'siklus' => 'A',
                'hari' => 'Senin',
                'waka' => ['nama' => 'Setiyo Winarko, S.Pd', 'nip' => '197210302003121002'],
                'pagi_koordinator' => 'Titin Sukmasari, S.Pd., M.Pd',
                'pagi_petugas' => [
                    'Septiani, S.Pd., M.Pd',
                    'Martiin, S.Pd',
                    'Winarsih, S.Pd, M.Pd',
                ],
                'siang_koordinator' => 'Lutfia Marsalina, S.Pd.I, M.Pd.',
                'siang_petugas' => [
                    'Nurul Azizah, S.Pd',
                    "Rifkotin Na'imah, S.Pd",
                    'Dra. Susakti Yuharini',
                ],
            ],
            [
                'siklus' => 'A',
                'hari' => 'Selasa',
                'waka' => ['nama' => 'Niken Hari Pratiwi, S.Psi., M.Pd', 'nip' => '198203032009012009'],
                'pagi_koordinator' => 'Lilik Suratmi, S.Pd',
                'pagi_petugas' => [
                    'Sulistyowati, SS',
                    'Wiwik Yuniarsih, S.Pd.',
                    'Sri Kusumastuti, S.Pd',
                ],
                'siang_koordinator' => 'Widodo, S.Pd',
                'siang_petugas' => [
                    'Kasmi, S.Pd., M.Pd',
                    'Siti Munawaroh, S.Kom.,M.Pd',
                    'Niken Dewi Hastika, S.Pd',
                ],
            ],
            [
                'siklus' => 'A',
                'hari' => 'Rabu',
                'waka' => ['nama' => 'Hardini Indahing Budi, S.E., M.Pd.', 'nip' => '198208222014072002'],
                'pagi_koordinator' => 'Elyana Frisca Monica, S.Pd',
                'pagi_petugas' => [
                    'Tutut Sriatin, S.Pd',
                    'Rika Okta Maulida, S.Ds.',
                    'Mufatiroh, S.Ag',
                ],
                'siang_koordinator' => 'Erwan Septiyono, S.Pd',
                'siang_petugas' => [
                    'Siswanti Purwaningsih, S.T., M.Pd',
                    'Shinta Indyar Shanty Susanto, S.Kom',
                    'Dhuana Putri Puspitasary, S.Pd',
                ],
            ],
            [
                'siklus' => 'A',
                'hari' => 'Kamis',
                'waka' => ['nama' => 'Hendro Suwignyo, ST', 'nip' => '197711122022211007'],
                'pagi_koordinator' => 'Danang Anjar Hymawanto, S.Pd',
                'pagi_petugas' => [
                    'Yuni Jiastuti, S.Pd',
                    'Yuli Ratnasari, S.Pd',
                    'Agus Pramono, S.Sn',
                ],
                'siang_koordinator' => 'Istiana Suhartati, S.T',
                'siang_petugas' => [
                    'Risqi Nur Imama, S.Tr.Par',
                    'Luluk Munfarida, S.Pd',
                    'Tuhu Eries Kudori, S.Sn',
                ],
            ],
            [
                'siklus' => 'A',
                'hari' => 'Jumat',
                'waka' => ['nama' => 'Fajar Luthfianto, S.Pd', 'nip' => '197808102023211005'],
                'pagi_koordinator' => 'Joko Priyanto, S.Kom',
                'pagi_petugas' => [
                    'Arif Setyobudi, S.Pd',
                    'Sunarti, S.Pd',
                    'Isti Mufadah, S.Pd',
                ],
                'siang_koordinator' => 'Agung Yulianto, S.Pd',
                'siang_petugas' => [
                    'Dra. Hanik Pangestuti',
                    'Andri Krisdianto, SE., M.Pd',
                    'Fitria Renytasari, S.Pd',
                ],
            ],

            // ================= SIKLUS B =================
            [
                'siklus' => 'B',
                'hari' => 'Senin',
                'waka' => ['nama' => 'Setiyo Winarko, S.Pd', 'nip' => '197210302003121002'],
                'pagi_koordinator' => 'Dwi Rini Manfaati, S.Pd',
                'pagi_petugas' => [
                    'Siti Khoiriyah, S.Pd',
                    'Peni Wulandari, S.Pd',
                    'Badrus Sulaiman, S.Pd.',
                ],
                'siang_koordinator' => 'Dwi Kuswanto, S.Pd',
                'siang_petugas' => [
                    "Elysa Yuli Nur'aini, S.Si",
                    'Dwi Nowa Setyandari, S.Pd',
                    "Mas'an Widodo, S.Pd. M.T",
                ],
            ],
            [
                'siklus' => 'B',
                'hari' => 'Selasa',
                'waka' => ['nama' => 'Niken Hari Pratiwi, S.Psi., M.Pd', 'nip' => '198203032009012009'],
                'pagi_koordinator' => 'Ayu Pusposorini, ST',
                'pagi_petugas' => [
                    'Rindang Rejeki, S.Pd',
                    'Diana Hartanti, S.T., M.Pd',
                    'Veronica Damay Rulitasari, S.Pd',
                ],
                'siang_koordinator' => 'Agustina Mardika Rini, S.Pd., M.Pd',
                'siang_petugas' => [
                    'Umi Kulsum, S.Pd',
                    'Ruly Dwi Setyaningrum, S.Kom',
                    'Sri Rahayu, S.Pd',
                ],
            ],
            [
                'siklus' => 'B',
                'hari' => 'Rabu',
                'waka' => ['nama' => 'Hardini Indahing Budi, S.E., M.Pd.', 'nip' => '198208222014072002'],
                'pagi_koordinator' => "Sa'ad Wazis Hiedayat, S.Pd",
                'pagi_petugas' => [
                    'Dra. Anik Indriani',
                    'Retno Widyastuti, S.Pd., M.Pd',
                    'Nur Eko Wahyuningsih, S.Pd',
                ],
                'siang_koordinator' => 'Dyah Esti Rahayu, S.Pd',
                'siang_petugas' => [
                    'Purwati, S.Pd',
                    'Ratih Dian Irawati, SE',
                    'Siti Maisaroh, S.Pd',
                ],
            ],
            [
                'siklus' => 'B',
                'hari' => 'Kamis',
                'waka' => ['nama' => 'Hendro Suwignyo, ST', 'nip' => '197711122022211007'],
                'pagi_koordinator' => 'Endang Ary Handayani, S.T., M.Pd',
                'pagi_petugas' => [
                    'Siti Umiharsih, S.Pd',
                    'Erna Rinawati, S.Pd',
                    'Astra Bella Flamboyan, S.Psi',
                ],
                'siang_koordinator' => 'Dian Mawarti, S.Pd',
                'siang_petugas' => [
                    'Ninik Sriwidayati, S.Pd., M.Pd',
                    'Endik Kuswantoro, S.Kom., M.T',
                    'Komariyah, S.Pd',
                ],
            ],
            [
                'siklus' => 'B',
                'hari' => 'Jumat',
                'waka' => ['nama' => 'Fajar Luthfianto, S.Pd', 'nip' => '197808102023211005'],
                'pagi_koordinator' => 'Kurnila Putri Islamawati, S.Pd',
                'pagi_petugas' => [
                    'Yani, S.Pd.',
                    'Titik Samsistini, S.Pd',
                    'Pipit Ambarwati, S.Pd',
                ],
                'siang_koordinator' => 'Nur Nastutisari, S.ST.Par.',
                'siang_petugas' => [
                    'Basuki Sarjono, S.Pd',
                    'Atih Wilupi, S.E, M.Pd',
                    'Nishfu Laili, S.Pd',
                ],
            ],
        ];

        foreach ($rosterData as $item) {
            $siklus = $item['siklus'];
            $hari = $item['hari'];
            $waka = $item['waka'];

            // Pagi Koordinator
            $koorPagi = $this->resolveGuru($item['pagi_koordinator']);
            JadwalPiketKbm::create([
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
                'siklus' => $siklus,
                'hari' => $hari,
                'shift' => 'Pagi',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '11:00:00',
                'peran' => 'koordinator',
                'urutan' => 1,
                'id_guru' => $koorPagi['id_guru'],
                'nama_guru' => $koorPagi['nama_guru'],
                'nip' => $koorPagi['nip'],
                'piket_waka_nama' => $waka['nama'],
                'piket_waka_nip' => $waka['nip'],
            ]);

            // Pagi Petugas
            foreach ($item['pagi_petugas'] as $idx => $pName) {
                $petugas = $this->resolveGuru($pName);
                JadwalPiketKbm::create([
                    'tahun_ajaran' => '2026/2027',
                    'semester' => 'Ganjil',
                    'siklus' => $siklus,
                    'hari' => $hari,
                    'shift' => 'Pagi',
                    'jam_mulai' => '07:00:00',
                    'jam_selesai' => '11:00:00',
                    'peran' => 'petugas',
                    'urutan' => $idx + 1,
                    'id_guru' => $petugas['id_guru'],
                    'nama_guru' => $petugas['nama_guru'],
                    'nip' => $petugas['nip'],
                    'piket_waka_nama' => $waka['nama'],
                    'piket_waka_nip' => $waka['nip'],
                ]);
            }

            // Siang Koordinator
            $koorSiang = $this->resolveGuru($item['siang_koordinator']);
            JadwalPiketKbm::create([
                'tahun_ajaran' => '2026/2027',
                'semester' => 'Ganjil',
                'siklus' => $siklus,
                'hari' => $hari,
                'shift' => 'Siang',
                'jam_mulai' => '11:00:00',
                'jam_selesai' => '15:00:00',
                'peran' => 'koordinator',
                'urutan' => 1,
                'id_guru' => $koorSiang['id_guru'],
                'nama_guru' => $koorSiang['nama_guru'],
                'nip' => $koorSiang['nip'],
                'piket_waka_nama' => $waka['nama'],
                'piket_waka_nip' => $waka['nip'],
            ]);

            // Siang Petugas
            foreach ($item['siang_petugas'] as $idx => $pName) {
                $petugas = $this->resolveGuru($pName);
                JadwalPiketKbm::create([
                    'tahun_ajaran' => '2026/2027',
                    'semester' => 'Ganjil',
                    'siklus' => $siklus,
                    'hari' => $hari,
                    'shift' => 'Siang',
                    'jam_mulai' => '11:00:00',
                    'jam_selesai' => '15:00:00',
                    'peran' => 'petugas',
                    'urutan' => $idx + 1,
                    'id_guru' => $petugas['id_guru'],
                    'nama_guru' => $petugas['nama_guru'],
                    'nip' => $petugas['nip'],
                    'piket_waka_nama' => $waka['nama'],
                    'piket_waka_nip' => $waka['nip'],
                ]);
            }
        }
    }

    private function resolveGuru(string $name): array
    {
        // Special manual mappings if name in DB has specific formatting
        if (stripos($name, 'Yani, S.Pd') !== false) {
            $g = Guru::where('nama_lengkap', 'like', 'Yani, S.Pd%')->first();
            if ($g) return ['id_guru' => $g->id_guru, 'nama_guru' => $g->nama_lengkap, 'nip' => $g->nip];
        }

        $clean = trim(preg_replace('/[,\.\']/', ' ', $name));
        $words = array_values(array_filter(explode(' ', $clean), fn($w) => strlen($w) > 2));

        $q = Guru::query();
        foreach ($words as $w) {
            $q->where('nama_lengkap', 'like', "%{$w}%");
        }
        $g = $q->first();

        if ($g) {
            return [
                'id_guru' => $g->id_guru,
                'nama_guru' => $g->nama_lengkap,
                'nip' => $g->nip,
            ];
        }

        // Fallback with first two words
        if (count($words) >= 2) {
            $g = Guru::where('nama_lengkap', 'like', "%{$words[0]}%")
                ->where('nama_lengkap', 'like', "%{$words[1]}%")
                ->first();
            if ($g) {
                return ['id_guru' => $g->id_guru, 'nama_guru' => $g->nama_lengkap, 'nip' => $g->nip];
            }
        }

        // Fallback first word
        $g = Guru::where('nama_lengkap', 'like', "%{$words[0]}%")->first();
        if ($g) {
            return ['id_guru' => $g->id_guru, 'nama_guru' => $g->nama_lengkap, 'nip' => $g->nip];
        }

        return [
            'id_guru' => null,
            'nama_guru' => $name,
            'nip' => null,
        ];
    }
}
