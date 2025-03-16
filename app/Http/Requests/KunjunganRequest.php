<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KunjunganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => 'required|date|unique:kunjungans,tanggal,NULL,id,pasien_id,' . $this->input('pasien_id'),
            'pasien_id' => 'required|max:255',
            'user_id' => 'max:255',
            'hasil_periksa' => 'max:255',
            'status' => 'max:255',
            'jenis' => 'max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal kunjungan harus diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.unique' => 'Pasien sudah memiliki kunjungan pada hari yang sama.',
            'pasien_id.max' => 'Pasien ID tidak boleh lebih dari 255 karakter.',
            'pasien_id.required' => 'Pastikan Data Pasien ada di sistem',
        ];
    }
}
