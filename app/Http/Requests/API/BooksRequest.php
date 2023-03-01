<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;

class BooksRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'isbn'  => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Titulo',
            'isbn'  => 'Codigo de barras do Livro'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Esse campo é obrigatório.'
        ];
    }
}
