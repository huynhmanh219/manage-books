<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => "required|max:255",
            'author' => "required|max:255",
            'published_year' => "required|numeric|digits:4",
            'isbn' => 'required|string|unique:books',
            'genre' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string'
        ];
    }
}
