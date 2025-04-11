<?php

namespace App\Http\Requests\Series;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeriesRequest extends FormRequest
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
            'series_date' => 'nullable|date',
            'status' => 'nullable|in:publish,hide',
            'image' => 'nullable|mimes:png,jpg,jpeg,svg|max:4000',
        ];
    }
}
