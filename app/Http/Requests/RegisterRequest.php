<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:12|confirmed',
            'phone' => 'required|string',
            'user_type' => 'required|string|in:sacco_member,chama,fundraiser',
        ];

        // If registering as SACCO member, require group_id
        if ($this->input('user_type') === 'sacco_member') {
            $rules['group_id'] = 'required|exists:groups,id';
        }

        // If creating a chama, require chama details
        if ($this->input('user_type') === 'chama') {
            $rules['chama_name'] = 'required|string|max:255';
            $rules['chama_description'] = 'nullable|string|max:1000';
            $rules['chama_location'] = 'nullable|string|max:255';
        }

        // If creating a fundraiser, require campaign details
        if ($this->input('user_type') === 'fundraiser') {
            $rules['campaign_title'] = 'required|string|max:255';
            $rules['campaign_description'] = 'required|string|max:2000';
            $rules['campaign_goal_amount'] = 'required|numeric|min:0.01';
        }

        return $rules;
    }
}
