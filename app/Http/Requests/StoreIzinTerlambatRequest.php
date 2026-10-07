<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IzinTerlambat;

class StoreIzinTerlambatRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya Guru Piket atau Admin yang boleh membuat izin terlambat
        $user = $this->user();
        return $user && in_array($user->role, ['guru_piket', 'admin', 'waka', 'waka_piket']);
    }

    public function rules(): array
    {
        return [
            'id_siswa'     => 'required|exists:siswa,id_siswa',
            'tanggal'      => 'required|date',
            'jam_masuk'    => 'required',
            'jam_ke_mulai' => 'required|max:10',
            'alasan'       => 'required|string|min:5|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'id_siswa.required'     => 'Pilihan siswa wajib diisi.',
            'id_siswa.exists'       => 'Data siswa yang dipilih tidak ditemukan dalam sistem.',
            'tanggal.required'      => 'Tanggal keterlambatan wajib diisi.',
            'tanggal.date'          => 'Format tanggal tidak valid.',
            'jam_masuk.required'    => 'Waktu kedatangan siswa di pos piket wajib diisi.',
            'jam_ke_mulai.required' => 'Jam pelajaran mulai masuk wajib diisi.',
            'alasan.required'       => 'Alasan keterlambatan wajib diisi.',
            'alasan.min'            => 'Alasan keterlambatan minimal 5 karakter agar jelas.',
            'alasan.max'            => 'Alasan keterlambatan maksimal 1000 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->filled(['id_siswa', 'tanggal', 'jam_ke_mulai'])) {
                $exists = IzinTerlambat::where('id_siswa', $this->id_siswa)
                    ->whereDate('tanggal', $this->tanggal)
                    ->where('jam_ke_mulai', $this->jam_ke_mulai)
                    ->whereIn('status', ['Menunggu', 'Disetujui'])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'id_siswa',
                        'Siswa ini sudah memiliki pengajuan izin terlambat aktif pada tanggal dan jam ke yang sama.'
                    );
                }
            }
        });
    }
}
