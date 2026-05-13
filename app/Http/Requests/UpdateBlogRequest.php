<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
    }

    public function rules(): array
    {
        return [
            'title'             => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:300'],
            'content'           => ['required', 'string'],
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'image'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'published_at'      => ['nullable', 'date'],
        ];
    }
}
