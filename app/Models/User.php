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

    public function isWakaKurikulum(): bool
    {
        return $this->role === 'waka_kurikulum' || ($this->role === 'waka' && $this->waka?->bidang === 'Kurikulum');
    }

    public function isWakaSdm(): bool
    {
        return $this->role === 'waka_sdm' || ($this->role === 'waka' && $this->waka?->bidang === 'SDM');
    }

    public function isWaliMurid(): bool
    {
        return $this->role === 'wali_murid';
    }

    public function isWaliKelas(): bool
    {
        if ($this->role === 'wali_kelas') {
            return true;
        }

        if ($this->waliKelas && $this->waliKelas->kelas()->exists()) {
            return true;
        }

        if ($this->guru) {
            return Kelas::whereHas('waliKelas', function ($q) {
                $q->where('nip', $this->guru->nip);
            })->exists();
        }

        return false;
    }

    public function isGuru(): bool
    {
        return in_array($this->role, ['guru_mapel', 'wali_kelas', 'guru_piket'])
            || $this->guru()->exists()
            || ($this->waliKelas && Guru::where('nip', $this->waliKelas->nip)->exists());
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
