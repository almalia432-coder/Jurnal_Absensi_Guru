<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Waka;

class DeactivateDemoWakaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:deactivate-demo-waka {--dry-run : Jalankan simulasi tanpa mengubah data database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nonaktifkan akun demo/legacy Waka (User ID 6, 11, 12, 146) beserta record waka terkait secara aman';

    /**
     * Target user ID yang akan dinonaktifkan.
     *
     * @var array<int>
     */
    protected const TARGET_USER_IDS = [6, 11, 12, 146];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $this->info('===============================================================');
        $this->info('  COMMAND: NONAKTIFKAN AKUN DEMO / LEGACY WAKA (SMKN 1 BOYOLANGU)');
        $this->info('===============================================================');

        if ($isDryRun) {
            $this->warn('>> [MODUS SIMULASI / DRY-RUN]');
            $this->warn('>> Tidak ada perubahan yang akan disimpan ke database.');
        } else {
            $this->alert('>> [MODUS EKSEKUSI NYATA]');
            if (!$this->confirm('Apakah Anda yakin ingin menonaktifkan akun demo Waka tersebut?', false)) {
                $this->info('Operasi dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $tableRows = [];

        foreach (self::TARGET_USER_IDS as $userId) {
            $user = User::with('waka')->find($userId);

            if (!$user) {
                $tableRows[] = [
                    $userId,
                    '-',
                    '-',
                    'User Tidak Ditemukan',
                    '-',
                    '-',
                    'Dilewati',
                ];
                continue;
            }

            $currentActive = $user->is_active ? 'Aktif (true)' : 'Nonaktif (false)';
            $wakaStatus = $user->waka
                ? ($user->waka->status_aktif ? 'Aktif (true)' : 'Nonaktif (false)')
                : 'Tidak Ada Record';

            $action = $isDryRun
                ? 'Akan dinonaktifkan (users.is_active=false, waka.status_aktif=false)'
                : 'BERHASIL DINONAKTIFKAN';

            $tableRows[] = [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $currentActive,
                $wakaStatus,
                $action,
            ];

            if (!$isDryRun) {
                $user->update(['is_active' => false]);
                if ($user->waka) {
                    $user->waka->update(['status_aktif' => false]);
                }
            }
        }

        $this->newLine();
        $this->table([
            'User ID',
            'Nama Pengguna',
            'Email',
            'Role',
            'Status Akun',
            'Status Waka',
            'Aksi / Rencana Aksi',
        ], $tableRows);

        $this->newLine();

        if ($isDryRun) {
            $this->info('Simulasi selesai. Data tetap utuh. Jalankan tanpa flag --dry-run jika sudah siap mengeksekusi secara permanen.');
        } else {
            $this->info('Akun demo/legacy Waka berhasil dinonaktifkan. Data historis approval dan tanda tangan tetap aman.');
        }

        return Command::SUCCESS;
    }
}
