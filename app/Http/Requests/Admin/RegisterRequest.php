<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Concerns\AdminRegistrationFields;
use App\Http\Requests\Concerns\ValidatesRegistrationEmailCode;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use AdminRegistrationFields;
    use ValidatesRegistrationEmailCode;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...$this->adminRegistrationFieldRules(),
            ...$this->registrationEmailCodeRules(),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->adminRegistrationFieldMessages(),
            ...$this->registrationEmailCodeMessages(),
        ];
    }
}
