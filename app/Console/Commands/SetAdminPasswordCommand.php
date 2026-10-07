<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SetAdminPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set-password 
                            {password? : Password baru untuk admin (jika tidak diisi, akan diminta atau di-generate)}
                            {--email=admin@smkn1boyolangu.sch.id : Email akun admin yang akan diubah}
                            {--generate : Generate password acak yang kuat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menetapkan atau mereset password akun administrator awal';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->option('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User dengan email [{$email}] tidak ditemukan.");
            return self::FAILURE;
        }

        $password = $this->argument('password');

        if ($this->option('generate')) {
            $password = Str::password(16);
            $this->info("Password acak dibuat: {$password}");
        } elseif (!$password) {
            $password = $this->secret('Masukkan password baru untuk admin');
            $confirm = $this->secret('Konfirmasi password baru');

            if ($password !== $confirm) {
                $this->error('Konfirmasi password tidak cocok.');
                return self::FAILURE;
            }
        }

        if (empty($password)) {
            $this->error('Password tidak boleh kosong.');
            return self::FAILURE;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("Password untuk [{$email}] berhasil diperbarui.");
        return self::SUCCESS;
    }
}
