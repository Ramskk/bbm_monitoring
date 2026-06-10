<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => [
                Rule::unique('vendor', 'kode')
                    ->ignore($this->id)
                    ->whereNull('deleted_at'),
            ],

            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',

            'email' => [
                'required_if:status,Aktif',
                'email',
                Rule::unique('vendor', 'email')
                    ->ignore($this->id)
                    ->whereNull('deleted_at'),
            ],

            'npwp' => 'nullable|string|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'kontak_telepon' => 'nullable|string|max:20',
            'rekening_bank' => 'nullable|string|max:100',
            'nama_bank' => 'nullable|string|max:100',
            'atas_nama' => 'nullable|string|max:100',

            // 🔥 HARUS SESUAI DATABASE
            'status' => 'required|in:Aktif,Non_Aktif',

            'deleted_at' => 'nullable|date',
        ];
    }
}