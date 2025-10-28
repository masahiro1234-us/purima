<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認証済みユーザーだけルートで制御するのでOK
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'コメントを入力してください。',
            'content.string'   => 'コメントは文字列で入力してください。',
            'content.max'      => 'コメントは255文字以内で入力してください。',
        ];
    }
}