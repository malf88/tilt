<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePetRequest extends FormRequest
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
            'name' => 'required|string|min:2|max:50|unique:pets,name',
            'pet_type' => 'required|string|in:dog,cat,dragon,fox,panda,tiger',
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
            'name.required' => 'O nome do pet é obrigatório.',
            'name.min' => 'O nome do pet deve ter pelo menos 2 caracteres.',
            'name.max' => 'O nome do pet não pode ter mais de 50 caracteres.',
            'name.unique' => 'Já existe um pet com este nome.',
            'pet_type.required' => 'Escolha um tipo de bichinho.',
            'pet_type.in' => 'Tipo de bichinho inválido.',
        ];
    }
}
