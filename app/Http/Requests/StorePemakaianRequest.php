<?php

namespace App\Http\Requests;

use App\Models\BBM;
use App\Models\Kendaraan;
use App\Models\Stok;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePemakaianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasAnyRole(['petugas_operasional', 'admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'jumlah_liter' => 'required|min:0.1',
            'odometer_sesudah' => 'required|numeric',
            'tanggal_pemakaian' => 'required|before_or_equal:now',
        ];
    }

    /**
     * Custom validation with helpers.
     */
    public function withValidator($validator): void
    {
        $vehicle = Kendaraan::find($this->kendaraan_id);

        if (! $vehicle) {
            return;
        }

        $bbm = BBM::find($vehicle->bbm_id);
        $stok = $bbm ? Stok::where('bbm_id', $bbm->id)->first() : null;

        // Cek odometer tidak turun
        if ($vehicle->odometer_terakhir > $this->odometer_sesudah) {
            $validator->errors()->add('odometer_sesudah', 'Odometer tidak boleh turun.');
        }

        // Cek jumlah liter ≤ kapasitas tangki
        if ($vehicle->kapasitas_tangki && $this->jumlah_liter > $vehicle->kapasitas_tangki) {
            $validator->errors()->add('jumlah_liter', "Jumlah liter melebihi kapasitas tangki {$vehicle->kapasitas_tangki} liter.");
        }

        // Cek kendaraan aktif
        if ($vehicle->status !== 'Aktif') {
            $validator->errors()->add('kendaraan_id', 'Kendaraan tidak aktif.');
        }

        // Cek stok cukup
        if (! $stok || $stok->jumlah < $this->jumlah_liter) {
            $validator->errors()->add('jumlah_liter', 'Stok '.($bbm->nama ?? '').' tidak cukup.');
        }
    }
}
