<?php

namespace App\Http\Requests;

use App\Models\BBM;
use App\Models\Vendor;
use App\Models\PO;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
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
    public function prepareForValidation(): array
    {
        return array_merge(parent::prepareForValidation(), [
            'total_nilai' => $this->jumlah_liter * $this->harga_per_liter,
        ]);
    }
}
