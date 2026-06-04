<?php

namespace App\Http\Requests;

use App\Models\BBM;
use App\Models\Stok;
use Illuminate\Foundation\Http\FormRequest;

class StoreStokMasukRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['admin_gudang', 'admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bbm_id' => 'required|exists:bbm,id',
            'jumlah' => 'required|numeric|min:0.1',
        ];
    }

    /**
     * Custom validation with helpers.
     */
    public function withValidator($validator): void
    {
        $bbm = BBM::find($this->bbm_id);

        // Cek stok tidak melebihi kapasitas maksimum
        if (!$bbm || $bbm->stok_maksimum && $this->jumlah > $bbm->stok_maksimum) {
            $validator->errors()->add('jumlah', "Jumlah melebihi kapasitas maksimum {$bbm->stok_maksimum} liter.");
        }

        // Cek referensi valid
        $refNo = $this->input('referensi_no') ?? null;
        $refType = $this->input('referensi_type') ?? null;
        $refId = $this->input('referensi_id') ?? null;

        if ($refType === 'PO' && $refNo && $refId) {
            $validator->rules('referensi_no', 'exists:po,no_po');
        }

        // Validasi harga per liter
        $price = $this->input('harga_per_liter') ?? 0;
        $validator->rules('harga_per_liter', 'numeric|min:0');
    }
}
