<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule as ValidationRuleAlias;
use Illuminate\Foundation\Http\FormRequest;

class RegistrarRequest extends FormRequest
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
     * @return array<string, ValidationRuleAlias|array|string>
     */
    public function rules(): array
    {
        return [
            'cliente' => 'required|string|max:255',
            'celular' => 'required|numeric|min:0',
            'producto' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'formaPago' => 'required|integer|in:1,2', //1: Contado, 2: Credito
            'abono' => 'nullable|numeric',
            'cantidad' => 'required|numeric|min:1',
        ];
    }
}
