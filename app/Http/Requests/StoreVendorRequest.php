<?php

namespace App\Http\Requests;

use App\Models\Vendor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode' => [
                Rule::unique('vendor', 'kode')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'kota' => 'nullable|string|max:100',
            'telepon' => 'nullable|string|max:20',
            'email' => [
                'required_if:status,active',
                Rule::email->unique('vendor', 'email')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'npwp' => 'nullable|string|max:100',
            'kontak_person' => 'nullable|string|max:100',
            'kontak_telepon' => 'nullable|string|max:20',
            'rekening_bank' => 'nullable|string|max:100',
            'nama_bank' => 'nullable|string|max:100',
            'atas_nama' => 'nullable|string|max:100',
            'status' => 'required|exists:vendor,status',
            'deleted_at' => 'nullable|date',
        ];
    }
}
