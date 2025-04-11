<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
            'title' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'post_date' => 'nullable|date',
            'status' => 'nullable|in:publish,hide',
            'content' => 'nullable',
            'image' => 'nullable|mimes:png,jpg,svg,jpeg|max:4000',
        ];
    }
}
