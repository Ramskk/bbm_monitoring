<?php

namespace App\Http\Requests;

use App\Models\BBM;
use App\Models\Kendaraan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKendaraanRequest extends FormRequest
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
            'nomor_polisi' => [
                Rule::unique('kendaraan', 'nomor_polisi')->ignore($this->id)->whereNull('deleted_at'),
            ],
            'nama' => 'required|string|max:100',
            'merek' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'tahun' => 'required|integer|min:1900|max:2030',
            'jenis' => 'required|exists:bbm,jenis',
            'bbm_id' => 'required|exists:bbm,id',
            'kapasitas_tangki' => 'required|numeric|min:0',
            'konsumsi_bbm_standar' => 'required|numeric|min:0',
            'odometer_awal' => 'required|numeric|min:0',
            'odometer_terakhir' => 'required|numeric|min:0',
            'departemen' => 'required|string|max:100',
            'pengemudi_default' => 'nullable|string|max:100',
            'status' => 'required|exists:kendaraan,status',
            'is_active' => 'boolean',
            'deleted_at' => 'nullable|date',
        ];
    }
}
