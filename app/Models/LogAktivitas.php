<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aksi',
        'deskripsi',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat log aktivitas secara otomatis
     *
     * @param string $aksi
     * @param string $deskripsi
     * @param mixed $model Model instance or model class name string
     * @param mixed $user User instance or user_id int
     * @return self
     */
    public static function catat(string $aksi, string $deskripsi, $model = null, $user = null): self
    {
        $resolvedUserId = null;
        if ($user instanceof User) {
            $resolvedUserId = $user->id;
        } elseif (is_numeric($user)) {
            $resolvedUserId = (int) $user;
        } elseif (\Illuminate\Support\Facades\Auth::check()) {
            $resolvedUserId = \Illuminate\Support\Facades\Auth::id();
        }

        $modelType = null;
        $modelId = null;

        if (is_object($model)) {
            $modelType = class_basename($model);
            $modelId = method_exists($model, 'getKey') ? $model->getKey() : ($model->id ?? null);
        } elseif (is_string($model)) {
            $modelType = class_basename($model);
        }

        $ip = '127.0.0.1';
        $ua = 'System';
        try {
            if (function_exists('request') && request()) {
                $ip = request()->ip() ?? '127.0.0.1';
                $ua = substr(request()->userAgent() ?? 'System', 0, 255);
            }
        } catch (\Throwable $e) {
            // fallback
        }

        return self::create([
            'user_id'    => $resolvedUserId,
            'aksi'       => $aksi,
            'deskripsi'  => $deskripsi,
            'model_type' => $modelType,
            'model_id'   => $modelId,
            'ip_address' => $ip,
            'user_agent' => $ua,
        ]);
    }

    /**
     * Scope untuk memfilter log berdasarkan role tertentu
     */
    public function scopeForRole($query, string $role)
    {
        return $query->whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        });
    }

    /**
     * Scope untuk aktivitas admin & operasional sistem
     */
    public function scopeAdminActivities($query)
    {
        return $query->where(function ($q) {
            $q->whereHas('user', function ($qu) {
                $qu->where('role', 'admin');
            })
            ->orWhereIn('model_type', [
                'User', 'Admin', 'Guru', 'Siswa', 'Kelas', 'Mapel', 
                'Jurusan', 'TahunAjaran', 'Import', 'Sistem', 'Backup'
            ]);
        });
    }
}

