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
            'email' => 'required|min:5|max:255|unique:users,email',
            'login' => 'required|min:3|max:25|unique:users,login',
            'password' => 'required|min:4|max:10',
            'role' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name'     => 'Nome',
            'role'     => 'Função',
            'email'    => 'Email',
            'login'    => 'Login',
            'password' => 'Senha',
        ];
    }

    public function messages()
    {
        return [
            'name.min'     => ':attribute muito pequeno.',
            'email.min'    => ':attribute muito pequeno.',
            'login.min'    => ':attribute muito pequeno.',
            'password.min' => ':attribute muito pequena, necessário ao menos 4 caracteres.',

            'name.max'     => ':attribute muito grande.',
            'email.max'    => ':attribute muito grande.',
            'login.max'    => ':attribute muito grande.',
            'password.max' => ':attribute muito grande, máximo 10 caracteres.',

            'name.required'     => 'Está faltando o :attribute.',
            'role.required'     => 'Está faltando a :attribute.',
            'email.required'    => 'Está faltando o :attribute.',
            'login.required'    => 'Está faltando o :attribute.',
            'password.required' => 'Está faltando a :attribute.',

            'email.unique' => 'O :attribute já está cadastrado!.',
            'login.unique' => 'O :attribute já está cadastrado!.',
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 500));
    }
}
