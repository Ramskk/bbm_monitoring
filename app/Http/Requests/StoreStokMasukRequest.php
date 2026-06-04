<?php

namespace App\Http\Requests;

use App\Models\BBM;
use App\Models\Stok;
use Illuminate\Contracts\Validation\ValidationRule;
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
     * @return array<string, ValidationRule|array<mixed>|string>
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

        if (! $bbm) {
            return;
        }

        $stok = Stok::where('bbm_id', $bbm->id)->first();
        $maksimum = $stok?->stok_maksimum;

        // Cek stok tidak melebihi kapasitas maksimum
        if ($maksimum && (($stok->jumlah + (float) $this->jumlah) > $maksimum)) {
            $validator->errors()->add('jumlah', "Jumlah melebihi kapasitas maksimum {$maksimum} liter.");
        }

        // Validasi harga per liter (jika diisi)
        $price = $this->input('harga_per_liter');
        if ($price !== null && (! is_numeric($price) || (float) $price < 0)) {
            $validator->errors()->add('harga_per_liter', 'Harga per liter tidak valid.');
        }
    }
}
