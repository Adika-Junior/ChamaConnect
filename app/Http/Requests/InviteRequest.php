<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->can('create', \App\Models\User::class);
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email',
        ];
    }
}
