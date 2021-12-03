<?php

namespace App\Http\Requests\Fornecedor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class FornecedorRequest extends FormRequest
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
            'fornecedor' => 'required|string|min:3|max:255',
            'telefone' => 'required|min:10|max:11|unique:fornecedores,telefone'
        ];
    }

    public function attributes()
    {
        return [
            'fornecedor' => 'Nome',
            'telefone' => 'Tel.\Cel.',
        ];
    }

    public function messages()
    {
        return [
            'fornecedor.min' => ':attribute muito pequeno.',
            'fornecedor.max' => ':attribute muito grande.',
            'fornecedor.required' => 'Necessita de um :attribute.',
            'telefone.unique' => 'Número de :attribute já cadastrado!.',
            'telefone.min' => 'Número de :attribute inválido!',
            'telefone.max' => 'Número de :attribute inválido!',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 500));
    }
}
