<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'title' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'slug' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'author' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
            'post_date' => $isUpdate ? 'nullable|date' : 'required|date',

            // Content blocks must be an array
            'content_blocks' => $isUpdate ? 'nullable|array' : 'required|array',
            'content_blocks.*.type' => $isUpdate ? 'nullable|in:paragraph,header,list,code,image' : 'required|in:paragraph,header,list,code,image',
            'content_blocks.*.data' => $isUpdate ? 'nullable|array' : 'required|array',

            // Paragraph block (text field required)
            'content_blocks.*.data.text' => 'nullable|string',

            // Header block (level is optional but constrained if present)
            'content_blocks.*.data.level' => 'nullable|integer|min:1|max:6',

            // List block (items are required if block type is 'list')
            'content_blocks.*.data.items' => 'nullable|array',
            'content_blocks.*.data.items.*.content' => 'nullable|string',
            'content_blocks.*.data.items.*.order' => 'nullable|integer',
            'content_blocks.*.data.items.*.style' => 'nullable|string',

            // Code block (code field required only if type is 'code')
            'content_blocks.*.data.code' => 'nullable|string',

            // Image block (image_url required only if type is 'image')
            'content_blocks.*.data.image_url' => 'nullable|url',

            // Meta data (optional for all types)
            'content_blocks.*.data.meta' => 'nullable|array',
        ];
    }
}
