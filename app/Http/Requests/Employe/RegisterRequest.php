<?php

namespace App\Http\Requests\Employe;

use App\Http\Requests\Concerns\EmployeRegistrationFields;
use App\Http\Requests\Concerns\ValidatesRegistrationEmailCode;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    use EmployeRegistrationFields;
    use ValidatesRegistrationEmailCode;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            ...$this->employeRegistrationFieldRules(),
            ...$this->registrationEmailCodeRules(),
        ];
    }

    public function messages(): array
    {
        return [
            ...$this->employeRegistrationFieldMessages(),
            ...$this->registrationEmailCodeMessages(),
        ];
    }
}
