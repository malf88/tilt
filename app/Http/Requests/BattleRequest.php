<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BattleRequest extends FormRequest
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
            'difficulty' => 'required|in:easy,medium,hard',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'difficulty.required' => 'A dificuldade da batalha é obrigatória.',
            'difficulty.in' => 'A dificuldade deve ser: easy, medium ou hard.',
        ];
    }
}
