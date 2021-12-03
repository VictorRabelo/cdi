<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CategoriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'categoria' => 'required|string|min:3|max:255',
            'subcategoria' => 'required|min:3|max:255|unique:categorias,subcategoria'
        ];
    }

    public function attributes()
    {
        return [
            'categoria' => 'Categoria',
            'subcategoria' => 'Subcategoria',
        ];
    }

    public function messages()
    {
        return [
            'categoria.min' => ':attribute muito pequeno.',
            'categoria.max' => ':attribute muito grande.',
            'categoria.required' => 'Necessita de uma :attribute.',
            'subcategoria.unique' => 'Número de :attribute já cadastrado!.',
            'subcategoria.min' => 'Número de :attribute inválido!',
            'subcategoria.max' => 'Número de :attribute inválido!',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 500));
    }
}
