<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|min:5|max:255|unique:Users,email',
            'login' => 'required|min:3|max:11',
            'password' => 'required|min:10|max:11'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Nome',
            'email' => 'Email',
            'login' => 'Login',
            'password' => 'Senha',
        ];
    }

    public function messages()
    {
        return [
            'name.min' => ':attribute muito pequeno.',
            'name.max' => ':attribute muito grande.',
            'name.required' => 'Necessita de um :attribute.',
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
