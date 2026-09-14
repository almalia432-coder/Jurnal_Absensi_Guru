@extends('layouts.waka_sdm')

@section('title', 'Data Guru & Tenaga Pendidik - Waka SDM')
@section('header_title', 'Direktori Guru & Pendidik')
@section('header_subtitle', 'Daftar guru aktif, NIP, status kepegawaian, dan data kontak')

@section('styles')
<style>
    .filter-card {
        background: white; border-radius: 16px; padding: 18px 24px;
        border: 1px solid #e5e9f2; margin-bottom: 24px;
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; box-shadow: 0 4px 14px rgba(0,0,0,0.02);
    }
    .filter-input {
        padding: 8px 14px; border-radius: 10px; border: 1.5px solid #cbd5e1;
        font-size: 13px; font-weight: 600; color: #1e293b; outline: none; font-family: inherit;
    }
    .filter-input:focus { border-color: #2563eb; }
    .btn-search {
        padding: 8px 16px; border-radius: 10px; background: #2563eb; color: white;
        font-size: 13px; font-weight: 700; border: none; cursor: pointer;
    }
    .section-card {
        background: white; border-radius: 18px; border: 1px solid #e5e9f2;
        box-shadow: 0 4px 16px rgba(0,0,0,0.02); overflow: hidden;
    }
    .data-table { width: 100%; border-collapse: collapse; text-align: left; }
    .data-table th {
        padding: 14px 20px; background: #f8fafc; font-size: 11.5px; font-weight: 800;
        color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;
    }
    .data-table td {
        padding: 16px 20px; border-bottom: 1px solid #f1f5f9; font-size: 13.5px; color: #334155; vertical-align: middle;
    }
    .avatar-sm {
        width: 36px; height: 36px; border-radius: 50%; background: #3b82f6; color: white;
        font-weight: 800; font-size: 13px; display: flex; align-items: center; justify-content: center;
    }
    .pagination-wrap { padding: 18px 24px; border-top: 1px solid #f1f5f9; }
</style>
@endsection

@section('content')
<div>
    <div class="filter-card">
        <div style="font-weight:700; font-size:14px; color:#0f172a;">
            Total Guru: {{ $guruList->total() }} Pendidik
        </div>
        <form action="{{ route('waka-sdm.guru') }}" method="GET" style="display:flex; align-items:center; gap:10px;">
            <input type="text" name="search" value="{{ $search }}" class="filter-input" placeholder="Cari nama atau NIP guru...">
            <button type="submit" class="btn-search"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
            @if($search)
                <a href="{{ route('waka-sdm.guru') }}" class="btn-search" style="background:#64748b; text-decoration:none;">Reset</a>
            @endif
        </form>
    </div>

    <div class="section-card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pendidik</th>
                        <th>NIP</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon / HP</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guruList as $idx => $g)
                        @php
                            $nama = $g->nama_lengkap;
                            $words = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z\s]/', '', $nama)));
                            $init = count($words) >= 2 ? strtoupper(substr($words[0],0,1).substr($words[1],0,1)) : 'GR';
                        @endphp
                        <tr>
                            <td>{{ $guruList->firstItem() + $idx }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="avatar-sm">{{ $init }}</div>
                                    <div style="font-weight:700; color:#0f172a;">{{ $nama }}</div>
                                </div>
                            </td>
                            <td>{{ $g->nip ?: '-' }}</td>
                            <td>{{ $g->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $g->no_hp ?: '-' }}</td>
                            <td>
                                <span style="background:#d1fae5; color:#059669; padding:4px 12px; border-radius:20px; font-size:11.5px; font-weight:700;">
                                    Aktif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:40px; color:#94a3b8;">
                                Tidak ada data guru ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">
            {{ $guruList->links() }}
        </div>
    </div>
</div>
@endsection
