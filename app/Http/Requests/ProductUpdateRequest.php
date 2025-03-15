<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|min:3|max:255|unique:products,name',
            'price' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string|min:3|max:255',
            'image_url' => 'sometimes|nullable|url',
        ];
    }

    public function messages()
    {
        return [
            'name.sometimes' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'name.unique' => 'Já existe um produto cadastrado com este nome.',
            
            'price.sometimes' => 'O preço é obrigatório.',
            'price.numeric' => 'O preço deve ser um número decimal válido (ex: 10.99).',
            
            'description.sometimes' => 'A descrição é obrigatória.',
            'description.string' => 'A descrição deve ser um texto.',
            
            'category.sometimes' => 'A categoria é obrigatória.',
            'category.string' => 'A categoria deve ser um texto.',
            'category.min' => 'A categoria deve ter no mínimo 3 caracteres.',
            'category.max' => 'A categoria não pode ter mais de 255 caracteres.',
            
            'image_url.url' => 'A URL da imagem deve ser um endereço válido.',
        ];
    }
}
