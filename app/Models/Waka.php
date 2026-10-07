<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Waka extends Model
{
    protected $table = 'waka';

    protected $fillable = [
        'user_id',
        'nip',
        'nama_lengkap',
        'jenis_kelamin',
        'no_hp',
        'bidang',
        'bidang_kode',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'bidang_kode' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
