<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePORequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin_pengadaan', 'admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'vendor_id' => 'required|exists:vendor,id',
            'bbm_id' => 'required|exists:bbm,id',
            'jumlah_liter' => 'required|min:1',
            'harga_per_liter' => 'required|min:1',
            'tanggal_po' => 'required|date',
            'tanggal_kirim_rencana' => 'after_or_equal:tanggal_po',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'total_nilai' => (float) $this->jumlah_liter * (float) $this->harga_per_liter,
        ]);
    }
}
