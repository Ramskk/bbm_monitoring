<?php

namespace App\Http\Requests;

use App\Models\BBM;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBBMRequest extends FormRequest
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
        // Ambil semua jenis BBM dari database
        $jenisList = BBM::distinct()
            ->pluck('jenis')
            ->toArray();

        return [
            'kode' => [
                Rule::unique('bbm', 'kode')
                    ->ignore($this->id)
                    ->whereNull('deleted_at'),
            ],

            'nama' => 'required|string|max:100',

            // Validasi langsung dari array (lebih aman & Laravel style)
            'jenis' => ['required', Rule::in($jenisList)],

            'harga_per_liter' => 'required|numeric|min:0',

            'is_active' => 'boolean',

            'keterangan' => 'nullable|string|max:255',

            'deleted_at' => 'nullable|date',
        ];
    }
}