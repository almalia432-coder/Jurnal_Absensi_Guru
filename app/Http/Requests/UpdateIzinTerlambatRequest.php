<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\IzinTerlambat;

class UpdateIzinTerlambatRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->role, ['guru_piket', 'admin']);
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
            'id_siswa.exists'       => 'Data siswa tidak ditemukan dalam sistem.',
            'tanggal.required'      => 'Tanggal keterlambatan wajib diisi.',
            'jam_masuk.required'    => 'Waktu kedatangan siswa wajib diisi.',
            'jam_ke_mulai.required' => 'Jam pelajaran mulai masuk wajib diisi.',
            'alasan.required'       => 'Alasan keterlambatan wajib diisi.',
            'alasan.min'            => 'Alasan keterlambatan minimal 5 karakter.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $id = $this->route('id') ?? $this->route('terlambat');
            $record = IzinTerlambat::find($id);

            if ($record && $record->status !== 'Menunggu' && $this->user()->role !== 'admin') {
                $validator->errors()->add(
                    'status',
                    'Izin terlambat ini sudah diproses dan tidak dapat diubah lagi.'
                );
            }

            if ($this->filled(['id_siswa', 'tanggal', 'jam_ke_mulai'])) {
                $exists = IzinTerlambat::where('id_siswa', $this->id_siswa)
                    ->where('id', '!=', $id)
                    ->whereDate('tanggal', $this->tanggal)
                    ->where('jam_ke_mulai', $this->jam_ke_mulai)
                    ->whereIn('status', ['Menunggu', 'Disetujui'])
                    ->exists();

                if ($exists) {
                    $validator->errors()->add(
                        'id_siswa',
                        'Siswa ini sudah memiliki permohonan izin terlambat aktif lain pada jam yang sama.'
                    );
                }
            }
        });
    }
}
