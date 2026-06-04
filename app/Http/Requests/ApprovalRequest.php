<?php

namespace App\Http\Requests;

use App\Models\Approval;
use App\Models\PO;
use App\Models\TransaksiBBM;
use Illuminate\Foundation\Http\FormRequest;

class ApprovalRequest extends FormRequest
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
            'keputusan' => 'required|in:approved,rejected',
            'catatan' => 'required_if:keputusan,rejected',
        ];
    }

    /**
     * Custom validation with helpers.
     */
    public function withValidator($validator): void
    {
        $keputusan = $this->input('keputusan');

        // Security: keputusan hanya boleh 'approved' atau 'rejected'
        // Tidak boleh value bebas dari frontend
        if (!in_array($keputusan, ['approved', 'rejected'])) {
            $validator->errors()->add('keputusan', 'Keputusan hanya boleh "approved" atau "rejected".');
        }

        // Catatan required jika keputusan rejected
        if ($keputusan === 'rejected' && !$this->has('catatan')) {
            $validator->errors()->add('catatan', 'Catatan diperlukan saat menolak.');
        }
    }
}
