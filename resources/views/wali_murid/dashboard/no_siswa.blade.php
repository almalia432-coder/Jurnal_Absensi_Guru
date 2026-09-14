@extends('layouts.wali_murid')

@section('title', 'Akun Belum Terhubung - Portal Wali Murid')
@section('header_title', 'Portal Wali Murid')
@section('header_subtitle', 'Sistem Jurnal Absensi SMKN 1 BOYOLANGU')

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;text-align:center;padding:40px 20px;">
    <div style="width:100px;height:100px;border-radius:28px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:44px;margin-bottom:24px;box-shadow:0 8px 24px rgba(217,119,6,0.2);">
        <i class="fa-solid fa-user-slash"></i>
    </div>
    <h2 style="font-size:22px;font-weight:800;color:#1b2559;margin-bottom:10px;">Data Siswa Tidak Ditemukan</h2>
    <p style="font-size:14px;color:#94a3b8;max-width:420px;line-height:1.7;margin-bottom:6px;">
        Akun Anda belum terhubung dengan data siswa manapun di sistem.
        Harap hubungi administrator sekolah untuk menghubungkan akun ini.
    </p>
    <p style="font-size:13px;color:#64748b;margin-bottom:28px;">
        Login sebagai: <strong>{{ $user->email }}</strong>
    </p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center;">
        <a href="{{ route('wali-murid.dashboard') }}"
           style="padding:10px 22px;background:#f1f5f9;color:#475569;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;border:1.5px solid #e2e8f0;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-rotate-right"></i> Coba Lagi
        </a>
        <button onclick="openLogoutModal()" type="button"
           style="padding:10px 22px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:12px;font-weight:700;font-size:14px;border:none;cursor:pointer;font-family:inherit;box-shadow:0 4px 12px rgba(239,68,68,0.3);display:inline-flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-right-from-bracket"></i> Keluar
        </button>
    </div>
</div>
@endsection
