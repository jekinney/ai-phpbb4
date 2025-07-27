<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTopicRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[\w\s\-_.!?()]+$/u'
            ],
            'content' => [
                'required',
                'string',
                'min:10',
                'max:10000'
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The topic title is required.',
            'title.min' => 'The topic title must be at least 3 characters.',
            'title.max' => 'The topic title cannot exceed 255 characters.',
            'title.regex' => 'The topic title contains invalid characters.',
            'content.required' => 'The post content is required.',
            'content.min' => 'The post content must be at least 10 characters.',
            'content.max' => 'The post content cannot exceed 10,000 characters.',
        ];
    }
}
