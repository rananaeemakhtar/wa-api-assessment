<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
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
            'chatroom_id'       =>  'exists:chatrooms,id', 
            'content'           =>  'required|string',
            'attachments'       =>  'array',
            'attachments.*'     =>  'file', 
        ];
    }

    public function messages()
    {
        return [
            'chatroom_id.required' => 'The chatroom ID is required.',
            'chatroom_id.exists' => 'The selected chatroom does not exist.',
            'content.required' => 'The message content is required.',
            'attachments.*.file' => 'Each attachment must be a valid file.'
        ];
    }
}
