<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->pasien?->id ?? $this->route('pasien');
        \Log::info('Pasien ID:', ['id' => $id]);

        return [
            'name' => 'string|max:255',
            'nik' => 'string|max:255' . $id,
            'alamat' => 'string|max:255',
            'jenis_kelamin' => 'string|max:255',
            'jenis_ktp' => 'string|max:255',
            'tanggal_lahir' => 'date',
            'village_id' => 'string|max:255',
            'district_id' => 'string|max:255',
            'regency_id' => 'string|max:255',
            'province_id' => 'string|max:255',
            'no_wa' => 'string|max:255',
            'keterangan' => 'string|max:255',
            'rt' => 'string|max:255',
            'rw' => 'string|max:255',
        ];
    }
}
