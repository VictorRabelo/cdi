<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthRequest extends FormRequest
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
            'login' => 'required|string|min:3|max:255',
            'password' => 'required|string|min:3',
        ];
    }

    public function attributes()
    {
        return [
            'login' => 'Login',
            'password' => 'Senha',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Necessita de um :attribute',
            'password.required' => 'Necessita de uma :attribute',
            
            'login.min' => 'Minimo 3 caracteres',
            'password.min' => 'Minimo 3 caracteres',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
