<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'photo'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /* Role Relationships to separate tables */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function waliKelas()
    {
        return $this->hasOne(WaliKelas::class, 'user_id');
    }

    public function guruPiket()
    {
        return $this->hasOne(GuruPiket::class, 'user_id');
    }

    public function guruMapel()
    {
        return $this->hasOne(GuruMapel::class, 'user_id');
    }

    public function satpam()
    {
        return $this->hasOne(Satpam::class, 'user_id');
    }

    public function kepalaSekolah()
    {
        return $this->hasOne(KepalaSekolah::class, 'user_id');
    }

    public function waka()
    {
        return $this->hasOne(Waka::class, 'user_id');
    }

    public function guru()
    {
        return $this->hasOne(Guru::class, 'user_id');
    }

    public function siswa()
    {
        return $this->hasOne(Siswa::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    public function unreadNotifikasi()
    {
        return $this->notifikasi()->where('is_read', false);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Dapatkan daftar portal yang dapat diakses oleh user beserta metadata.
     *
     * @param \Carbon\Carbon|null $date
     * @return array
     */
    public function availablePortals(?\Carbon\Carbon $date = null): array
    {
        return \App\Support\PortalResolver::resolve($this, $date);
    }

    /**
     * Dapatkan hanya daftar kunci portal (string array) milik user.
     *
     * @param \Carbon\Carbon|null $date
     * @return array
     */
    public function availablePortalKeys(?\Carbon\Carbon $date = null): array
    {
        return \App\Support\PortalResolver::getPortalKeys($this, $date);
    }

    /**
     * Periksa apakah user memiliki akses ke salah satu portal yang ditentukan.
     * Admin selalu bernilai true.
     *
     * @param string ...$portals
     * @return bool
     */
    public function hasPortal(string ...$portals): bool
    {
        return \App\Support\PortalResolver::hasAccess($this, $portals);
    }

    /**
     * Dapatkan portal utama (default) bagi user saat pertama kali login / redirect /.
     *
     * @return string|null
     */
    public function defaultPortal(): ?string
    {
        return \App\Support\PortalResolver::getDefaultPortal($this);
    }

    public function isWaka(): bool
    {
        return $this->hasPortal('waka_kurikulum', 'waka_sdm', 'waka_kesiswaan')
            || in_array($this->role, ['waka', 'waka_kurikulum', 'waka_sdm'])
            || $this->waka()->exists();
    }

    public function isWakaKurikulum(): bool
    {
        return $this->hasPortal('waka_kurikulum')
            || $this->role === 'waka_kurikulum'
            || ($this->isWaka() && str_contains(strtolower($this->waka?->bidang ?? ''), 'kurikulum'));
    }

    public function isWakaSdm(): bool
    {
        return $this->hasPortal('waka_sdm')
            || $this->role === 'waka_sdm'
            || ($this->isWaka() && str_contains(strtolower($this->waka?->bidang ?? ''), 'sdm'));
    }

    public function isWakaKesiswaan(): bool
    {
        return $this->hasPortal('waka_kesiswaan')
            || ($this->isWaka() && (str_contains(strtolower($this->waka?->bidang ?? ''), 'kesiswaan') || str_contains(strtolower($this->waka?->bidang ?? ''), 'kedisiplinan')));
    }

    public function isWaliMurid(): bool
    {
        return $this->hasPortal('wali_murid') || $this->role === 'wali_murid';
    }

    public function isWaliKelas(): bool
    {
        return $this->hasPortal('wali_kelas');
    }

    public function isGuru(): bool
    {
        // Akun bersama meja piket tidak mengajar
        if ($this->email === 'waka.piket@smkn1boyolangu.sch.id' || ($this->waka && $this->waka->bidang === 'Piket KBM')) {
            return false;
        }

        return in_array($this->role, ['guru_mapel', 'wali_kelas', 'guru_piket', 'waka', 'waka_kurikulum', 'waka_sdm'])
            || $this->guru()->exists()
            || ($this->waka && Guru::where('nip', $this->waka->nip)->exists())
            || ($this->waliKelas && Guru::where('nip', $this->waliKelas->nip)->exists());
    }

    public function hasTeachingDuty(): bool
    {
        // Akun dinas / bersama Waka Piket tidak memiliki tugas mengajar
        if ($this->email === 'waka.piket@smkn1boyolangu.sch.id' || ($this->waka && $this->waka->bidang === 'Piket KBM')) {
            return false;
        }

        if ($this->role === 'admin') {
            return true;
        }

        if ($this->role === 'guru_mapel') {
            return true;
        }

        if ($this->guru()->exists()) {
            return true;
        }

        if ($this->isWaka()) {
            return true;
        }

        if ($this->waliKelas && Guru::where('nip', $this->waliKelas->nip)->exists()) {
            return true;
        }

        return false;
    }

    public function getKelasBinaanAttribute(): ?Kelas
    {
        if ($this->waliKelas && $this->waliKelas->kelas) {
            return $this->waliKelas->kelas;
        }

        if ($this->guru) {
            return Kelas::whereHas('waliKelas', function ($q) {
                $q->where('nip', $this->guru->nip);
            })->first();
        }

        return null;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
